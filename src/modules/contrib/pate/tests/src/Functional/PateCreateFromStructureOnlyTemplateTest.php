<?php

namespace Drupal\Tests\pate\Functional;

use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\paragraphs\FunctionalJavascript\ParagraphsTestBaseTrait;

/**
 * Tests that we can create nodes out of (structure-only) templates.
 *
 * @group pate
 */
class PateCreateFromStructureOnlyTemplateTest extends BrowserTestBase {

  use ParagraphsTestBaseTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'block',
    'node',
    'pate',
    'paragraphs',
    'entity_reference_revisions',
    'replicate',
    'system',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * An admin user.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $adminUser;

  /**
   * An editor user.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $editorUser;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    // Place some blocks to make our lives easier down the road.
    $this->drupalPlaceBlock('system_breadcrumb_block');
    $this->drupalPlaceBlock('local_tasks_block');
    $this->drupalPlaceBlock('local_actions_block');
    $this->drupalPlaceBlock('page_title_block');

    $this->adminUser = $this->drupalCreateUser([
      'manage page templates',
      'use page templates',
      'administer content types',
      'administer nodes',
      'bypass node access',
    ]);

  }

  /**
   * Tests that nodes can be created from a template.
   */
  public function testCreateFromStructureOnlyTemplate() {
    $session = $this->getSession();
    $page = $session->getPage();
    $assert_session = $this->assertSession();
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');

    $this->drupalLogin($this->adminUser);

    $type1 = $this->drupalCreateContentType([
      'type' => 'one',
      'name' => 'Type One',
    ]);
    $type1->setThirdPartySetting('pate', 'is_templatable', TRUE)
      ->save();

    // Create a paragraph with a text field and add it to our test content type.
    $this->addParagraphsType("paragraph_pate");
    $this->addFieldtoParagraphType("paragraph_pate", "field_text", 'string');
    $this->addParagraphsField($type1->id(), 'field_paragraphs', 'node');
    // We want a nested paragraph there as well.
    $this->addParagraphsField('paragraph_pate', 'nested_paragraph_field', 'paragraph');

    // Create a node that has a couple of paragraphs to be used as template.
    $paragraph_child = Paragraph::create([
      'type' => 'paragraph_pate',
      'field_text' => 'I am some text at the nested paragraph',
    ]);
    $paragraph_child->save();
    $paragraph_parent = Paragraph::create([
      'type' => 'paragraph_pate',
      'field_text' => 'I am some text at the parent paragraph',
      'nested_paragraph_field' => [
        'target_id' => $paragraph_child->id(),
        'target_revision_id' => $paragraph_child->getRevisionId(),
      ],
    ]);
    $paragraph_parent->save();
    $node1 = $this->drupalCreateNode([
      'type' => 'one',
      'title' => 'First template',
      'pate_is_template' => FALSE,
      'pate_structure_only' => FALSE,
      'body' => ['value' => 'Body dummy text on first template'],
      'field_paragraphs' => [
        'target_id' => $paragraph_parent->id(),
        'target_revision_id' => $paragraph_parent->getRevisionId(),
      ],
      'status' => FALSE,
    ]);

    // Convert it to template using the UI so we can test the form elements.
    $this->drupalGet("/node/{$node1->id()}/templatize");
    $assert_session->pageTextContains("Convert {$node1->label()} into a page template");
    $radio_all_content = $assert_session->elementExists('css', 'input[name="pate_structure_only"][value="0"]');
    $this->assertTrue($radio_all_content->isChecked());
    $radio_structure_only = $assert_session->elementExists('css', 'input[name="pate_structure_only"][value="1"]');
    $this->assertFalse($radio_structure_only->isChecked());

    // Save and verify default behavior is kept (ie copy all content).
    $page->pressButton('Convert into template');
    $assert_session->pageTextContains("Node {$node1->label()} has been converted into a template. It can no longer be modified, but you can switch this operation back and convert it into a normal node at any point");
    $node1 = $node_storage->loadUnchanged($node1->id());
    $this->assertTrue((bool) $node1->pate_is_template->value);
    $this->assertFalse((bool) $node1->pate_structure_only->value);

    // Hit the create-from-template URL and see what happens.
    $this->drupalGet("/node/{$node1->id()}/create-from-template");
    // Verify that the pate_template=123 query param is there.
    // We don't use ::addressMatches() here since it will strip out query
    // params from the current URL before comparing.
    $current_url = $this->getSession()->getCurrentUrl();
    $this->assertMatchesRegularExpression('#/node/[\d]+/edit\?pate_template=' . $node1->id() . '#', $current_url);
    $nodes = \Drupal::entityQuery('node')
      ->condition('type', 'one')
      ->condition('title', $node1->label())
      ->accessCheck(FALSE)
      ->sort('nid', 'DESC')
      ->execute();
    // We still have only one node with that same title (the template itself).
    $this->assertEquals(1, count($nodes));
    // The last node created has a unique title.
    $expected_title_prefix = 'New Type One (' . $node1->label() . ') - ';
    $nodes = \Drupal::entityQuery('node')
      ->condition('type', 'one')
      ->condition('title', $this->getDatabaseConnection()->escapeLike($expected_title_prefix) . '%', 'LIKE')
      ->accessCheck(FALSE)
      ->sort('nid', 'DESC')
      ->range(0, 1)
      ->execute();
    $this->assertEquals(1, count($nodes));
    $new_nid = reset($nodes);
    $this->assertNotEquals($new_nid, $node1->id());
    // We are editing the new node.
    $assert_session->addressEquals("/node/{$new_nid}/edit");
    $assert_session->pageTextContains('Editing node created from template');
    $new_node = \Drupal::entityTypeManager()->getStorage('node')
      ->load($new_nid);
    $this->assertEmpty($new_node->pate_is_template->value);
    // The new node form has dummy text in all fields.
    $body = $assert_session->elementExists('css', 'textarea#edit-body-0-value');
    $this->assertSame('Body dummy text on first template', $body->getValue());
    $parent_paragraph_text_field = $assert_session->elementExists('css', 'input#edit-field-paragraphs-0-subform-field-text-0-value');
    $this->assertSame('I am some text at the parent paragraph', $parent_paragraph_text_field->getValue());
    $nested_paragraph_text_field = $assert_session->elementExists('css', 'input#edit-field-paragraphs-0-subform-nested-paragraph-field-0-subform-field-text-0-value');
    $this->assertSame('I am some text at the nested paragraph', $nested_paragraph_text_field->getValue());

    // Re-convert the template into a normal node and back into a template
    // using the UI, so we can set the structure-only flag now to TRUE.
    $this->drupalGet("/node/{$node1->id()}/templatize");
    $page->pressButton('Convert into normal node');
    $assert_session->pageTextContains("Node {$node1->label()} has been converted into a normal node");
    $this->drupalGet("/node/{$node1->id()}/templatize");
    // The test driver won't allow us to change the radio value by clicking on
    // it, so we just submit the form with the values we know are good for us.
    $edit = [
      'pate_structure_only' => TRUE,
    ];
    $this->submitForm($edit, 'Convert into template');
    $assert_session->pageTextContains("Node {$node1->label()} has been converted into a template");
    $node1 = $node_storage->loadUnchanged($node1->id());
    $this->assertTrue((bool) $node1->pate_is_template->value);
    $this->assertTrue((bool) $node1->pate_structure_only->value);

    $this->drupalGet("/node/{$node1->id()}/create-from-template");
    // Verify that the pate_template=123 query param is there.
    // We don't use ::addressMatches() here since it will strip out query
    // params from the current URL before comparing.
    $current_url = $this->getSession()->getCurrentUrl();
    $this->assertMatchesRegularExpression('#/node/[\d]+/edit\?pate_template=' . $node1->id() . '#', $current_url);
    $nodes = \Drupal::entityQuery('node')
      ->condition('type', 'one')
      ->condition('title', $node1->label())
      ->accessCheck(FALSE)
      ->sort('nid', 'DESC')
      ->execute();
    // We still have only one node with that same title (the template itself).
    $this->assertEquals(1, count($nodes));
    // The last node created has a unique title.
    $expected_title_prefix = 'New Type One (' . $node1->label() . ') - ';
    $nodes = \Drupal::entityQuery('node')
      ->condition('type', 'one')
      ->condition('title', $this->getDatabaseConnection()->escapeLike($expected_title_prefix) . '%', 'LIKE')
      ->accessCheck(FALSE)
      ->sort('nid', 'DESC')
      ->range(0, 1)
      ->execute();
    $this->assertEquals(1, count($nodes));
    $new_nid = reset($nodes);
    $this->assertNotEquals($new_nid, $node1->id());
    // We are editing the new node.
    $assert_session->addressEquals("/node/{$new_nid}/edit");
    $assert_session->pageTextContains('Editing node created from template');
    $new_node = \Drupal::entityTypeManager()->getStorage('node')
      ->load($new_nid);
    $this->assertEmpty($new_node->pate_is_template->value);
    $this->assertEmpty($new_node->pate_structure_only->value);
    // The new node form has empty text in all fields, but their elements
    // (inside nested paragraphs) are there.
    $body = $assert_session->elementExists('css', 'textarea#edit-body-0-value');
    $this->assertSame('', $body->getValue());
    $parent_paragraph_text_field = $assert_session->elementExists('css', 'input#edit-field-paragraphs-0-subform-field-text-0-value');
    $this->assertSame('', $parent_paragraph_text_field->getValue());
    $nested_paragraph_text_field = $assert_session->elementExists('css', 'input#edit-field-paragraphs-0-subform-nested-paragraph-field-0-subform-field-text-0-value');
    $this->assertSame('', $nested_paragraph_text_field->getValue());
  }

}
