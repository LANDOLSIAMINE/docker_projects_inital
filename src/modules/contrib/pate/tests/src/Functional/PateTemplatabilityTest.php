<?php

namespace Drupal\Tests\pate\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the templatability of nodes.
 *
 * @group pate
 */
class PateTemplatabilityTest extends BrowserTestBase {

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
   * Tests that content types can be flagged for being "templatable".
   */
  public function testContentTypeThirdPartySettings() {
    $assert_session = $this->assertSession();

    $this->drupalLogin($this->adminUser);

    // Create a few content types to be used in the test.
    $type1 = $this->drupalCreateContentType([
      'type' => 'one',
      'name' => 'Type One',
    ]);
    $type2 = $this->drupalCreateContentType([
      'type' => 'two',
      'name' => 'Type Two',
    ]);

    // Create a few nodes as well.
    $node1 = $this->drupalCreateNode([
      'type' => 'one',
      'title' => 'First node',
    ]);
    $node2 = $this->drupalCreateNode([
      'type' => 'two',
      'title' => 'Second node',
    ]);

    // First verify our local task isn't in any of them.
    $this->drupalGet($node1->toUrl());
    $assert_session->pageTextContains($node1->getTitle());
    $assert_session->elementNotExists('css', "a[href*='/node/{$node1->id()}/templatize']");
    $is_templatable = $type1->getThirdPartySetting('pate', 'is_templatable');
    $this->assertEmpty($is_templatable);
    $this->drupalGet($node2->toUrl());
    $assert_session->pageTextContains($node2->getTitle());
    $assert_session->elementNotExists('css', "a[href*='/node/{$node2->id()}/templatize']");
    $is_templatable = $type2->getThirdPartySetting('pate', 'is_templatable');
    $this->assertEmpty($is_templatable);

    // Enable it in the first type and check again.
    $this->drupalGet('/admin/structure/types/manage/one');
    $assert_session->elementExists('css', 'input[name="pate[is_templatable]"]')
      ->check();
    $assert_session->elementExists('css', 'input#edit-submit')
      ->click();
    $assert_session->pageTextContains("The content type {$type1->label()} has been updated");
    $type1 = \Drupal::entityTypeManager()->getStorage('node_type')
      ->loadUnchanged('one');
    $is_templatable = $type1->getThirdPartySetting('pate', 'is_templatable');
    $this->assertTrue($is_templatable);

    $this->drupalGet($node1->toUrl());
    $assert_session->pageTextContains($node1->getTitle());
    $assert_session->elementExists('css', "a[href*='/node/{$node1->id()}/templatize']");
    $this->drupalGet($node2->toUrl());
    $assert_session->pageTextContains($node2->getTitle());
    $assert_session->elementNotExists('css', "a[href*='/node/{$node2->id()}/templatize']");

    // A regular user can't see the local task.
    $this->drupalLogout();
    $this->drupalLogin($this->editorUser);
    $this->drupalGet($node1->toUrl());
    $assert_session->pageTextContains($node1->getTitle());
    $assert_session->elementNotExists('css', "a[href*='/node/{$node1->id()}/templatize']");
    // Nor can they access the form directly.
    $this->drupalGet("/node/{$node1->id()}/templatize");
    $assert_session->statusCodeEquals(403);
  }

  /**
   * Tests that nodes can be templatized and vice-versa.
   */
  public function testContentTemplatizationProcess() {
    $assert_session = $this->assertSession();

    $this->drupalLogin($this->adminUser);

    $type1 = $this->drupalCreateContentType([
      'type' => 'one',
      'name' => 'Type One',
    ]);
    $type1->setThirdPartySetting('pate', 'is_templatable', TRUE)
      ->save();
    $node1 = $this->drupalCreateNode([
      'type' => 'one',
      'title' => 'First node',
      'status' => TRUE,
    ]);

    $this->drupalGet($node1->toUrl());
    // The "Edit" tab is there.
    $assert_session->elementExists('css', "a[href*='/node/{$node1->id()}/edit']");
    // Our basefield defaults to FALSE.
    $this->assertEmpty($node1->pate_is_template->value);
    // Click the "Page Template" tab.
    $assert_session->elementExists('css', "a[href*='/node/{$node1->id()}/templatize']")
      ->click();
    // A published node cannot be templatized.
    $assert_session->elementTextContains('css', 'h1', 'Convert into template');
    $assert_session->pageTextContains('This node is published! Because templates cannot be modified, you can only convert into a template nodes that are unpublished. Please clone or recreate this content as unpublished version and try again.');
    $assert_session->elementNotExists('css', '#edit-submit');
    $assert_session->elementNotExists('css', '#edit-cancel');
    // Unpublish the node and try again.
    $node1->setUnpublished()->save();
    $this->drupalGet("/node/{$node1->id()}/templatize");
    $assert_session->elementTextContains('css', 'h1', "Convert {$node1->getTitle()} into a page template");
    $assert_session->pageTextNotContains('This node is published!');
    $assert_session->pageTextContains('A template can no longer be modified. Proceed with converting this page into a template?');
    $submit_button = $assert_session->elementExists('css', '#edit-submit');
    $this->assertSame('Convert into template', $submit_button->getValue());
    $cancel_button = $assert_session->elementExists('css', '#edit-cancel');
    // Canceling takes us to the node canonical page.
    $cancel_button->press();
    $assert_session->addressEquals("/node/{$node1->id()}");
    // Try again, this time press the submit button.
    $assert_session->elementExists('css', "a[href*='/node/{$node1->id()}/templatize']")
      ->click();
    $assert_session->elementExists('css', '#edit-submit')
      ->press();
    $assert_session->pageTextContains("Node {$node1->getTitle()} has been converted into a template. It can no longer be modified, but you can switch this operation back and convert it into a normal node at any point.");
    $node1 = \Drupal::entityTypeManager()->getStorage('node')
      ->loadUnchanged($node1->id());
    // Our basefield now is flagged.
    $this->assertNotEmpty($node1->pate_is_template->value);
    // And because of that some things have changed. For example, we no longer
    // have the "Edit" tab visible.
    $this->drupalGet($node1->toUrl());
    $assert_session->elementNotExists('css', "a[href*='/node/{$node1->id()}/edit']");
    // When we click "Page Template", we are now offered to Un-templatize it.
    $assert_session->elementExists('css', "a[href*='/node/{$node1->id()}/templatize']")
      ->click();
    $assert_session->elementTextContains('css', 'h1', "Convert {$node1->getTitle()} back into a normal node");
    $assert_session->pageTextNotContains('This node is published!');
    $assert_session->pageTextContains('This operation will convert this template back into a normal node. Proceed?');
    $submit_button = $assert_session->elementExists('css', '#edit-submit');
    $this->assertSame('Convert into normal node', $submit_button->getValue());
    // Go ahead and verify the action takes place.
    $assert_session->elementExists('css', '#edit-submit')
      ->press();
    $assert_session->addressEquals("/node/{$node1->id()}");
    $assert_session->pageTextContains("Node {$node1->getTitle()} has been converted into a normal node");
    $node1 = \Drupal::entityTypeManager()->getStorage('node')
      ->loadUnchanged($node1->id());
    $this->assertEmpty($node1->pate_is_template->value);
    $assert_session->elementExists('css', "a[href*='/node/{$node1->id()}/edit']");
  }

}
