<?php

namespace Drupal\Tests\pate\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Tests that we can check templates per type in an individual page.
 *
 * @group pate
 */
// @codingStandardsIgnoreFile
class PateTemplatesPerTypeTest extends WebDriverTestBase {

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

    $this->adminUser = $this->drupalCreateUser(array(
      'manage page templates',
      'use page templates',
      'administer content types',
      'administer nodes',
      'bypass node access',
    ));
  }

  /**
   * Tests that we can check all templates for a given type in a page.
   */
  public function testTemplatesForTypeList() {
    $assert_session = $this->assertSession();
    $session = $this->getSession();
    $page = $session->getPage();

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
      'title' => 'First node - NOT TEMPLATE',
    ]);
    $node2 = $this->drupalCreateNode([
      'type' => 'one',
      'title' => 'Second node - TEMPLATE',
      'pate_is_template' => TRUE,
      'status' => FALSE,
    ]);
    $node3 = $this->drupalCreateNode([
      'type' => 'two',
      'title' => 'Third node - NOT TEMPLATE',
    ]);
    $node4 = $this->drupalCreateNode([
      'type' => 'two',
      'title' => 'Fourth node - TEMPLATE',
      'pate_is_template' => TRUE,
      'status' => FALSE,
    ]);

    // Check what we have in the page that lists them.
    $this->drupalGet("/node/one/templates");
    $assert_session->pageTextContains('Available Type One templates');
    $assert_session->pageTextContains('Second node - TEMPLATE');
    $preview_link = $assert_session->elementExists('css', '.pate-templates-template a[href*="/node/' . $node2->id() . '/pate-preview"]');
    $assert_session->elementExists('css', '.pate-templates-template a[href*="/node/' . $node2->id() . '/create-from-template"]');

    // Check the preview displays the node in a modal.
    $preview_link->click();
    $cta_wrapper = $assert_session->waitForElementVisible('css', '.ui-dialog #drupal-modal .pate-cta-link-wrapper');
    $this->assertNotNull($cta_wrapper);
    $assert_session->elementExists('css', '.ui-dialog #drupal-modal .pate-template-preview');
    $session->switchToIFrame("pate-frame-id-{$node2->id()}");
    $session->wait(200);
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->pageTextContains($node2->getTitle());
    $session->switchToIFrame();
    $session->wait(200);
    $assert_session->assertWaitOnAjaxRequest();

    // Create a new one and see it appears there directly.
    $node5 = $this->drupalCreateNode([
      'type' => 'one',
      'title' => 'Fifth node - TEMPLATE',
      'pate_is_template' => TRUE,
      'status' => FALSE,
    ]);
    $this->drupalGet("/node/one/templates");
    $assert_session->pageTextContains('Available Type One templates');
    $assert_session->pageTextContains('Second node - TEMPLATE');
    $assert_session->pageTextContains('Fifth node - TEMPLATE');
  }

}
