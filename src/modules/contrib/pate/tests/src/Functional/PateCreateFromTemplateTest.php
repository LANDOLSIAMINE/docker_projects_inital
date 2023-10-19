<?php

namespace Drupal\Tests\pate\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests that we can create nodes out of templates.
 *
 * @group pate
 */
class PateCreateFromTemplateTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'block',
    'node',
    'pate',
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
      'administer content types',
      'administer nodes',
      'bypass node access',
    ]);

    $this->editorUser = $this->drupalCreateUser([
      'use page templates',
      'bypass node access',
    ]);
  }

  /**
   * Tests that nodes can be created from a template.
   */
  public function testCreateFromTemplate() {
    $assert_session = $this->assertSession();

    $this->drupalLogin($this->editorUser);

    $type1 = $this->drupalCreateContentType([
      'type' => 'one',
      'name' => 'Type One',
    ]);
    $type1->setThirdPartySetting('pate', 'is_templatable', TRUE)
      ->save();
    $node1 = $this->drupalCreateNode([
      'type' => 'one',
      'title' => 'First node',
      'pate_is_template' => TRUE,
      'status' => FALSE,
    ]);

    // There is just one node with this title.
    $nodes = \Drupal::entityQuery('node')
      ->condition('type', 'one')
      ->condition('title', 'First node')
      ->accessCheck(FALSE)
      ->sort('nid', 'DESC')
      ->execute();
    $this->assertEquals(1, count($nodes));

    // Editors can't edit or delete this content.
    $this->assertFalse($node1->access('update', $this->editorUser));
    $this->assertFalse($node1->access('delete', $this->editorUser));

    // Admins can delete but not edit.
    $this->assertFalse($node1->access('update', $this->adminUser));
    $this->assertTrue($node1->access('delete', $this->adminUser));

    // Hit the create-from-template URL and see what happens.
    $this->drupalGet("/node/{$node1->id()}/create-from-template");
    // Verify that the pate_template=123 query param is there.
    // We don't use ::addressMatches() here since it will strip out query
    // params from the current URL before comparing.
    $current_url = $this->getSession()->getCurrentUrl();
    $this->assertMatchesRegularExpression('#/node/[\d]+/edit\?pate_template=' . $node1->id() . '#', $current_url);
    $nodes = \Drupal::entityQuery('node')
      ->condition('type', 'one')
      ->condition('title', 'First node')
      ->accessCheck(FALSE)
      ->sort('nid', 'DESC')
      ->execute();
    // We still have only one node with that same title (the template itself).
    $this->assertEquals(1, count($nodes));
    // The last node created has a unique title.
    $expected_title_prefix = 'New Type One (First node) - ';
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
  }

}
