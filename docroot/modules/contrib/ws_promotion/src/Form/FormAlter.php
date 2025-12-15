<?php

namespace Drupal\ws_promotion\Form;

use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;


/**
 * Helper class for alter form.
 */
class FormAlter {

  use DependencySerializationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs an RandomizerPromotion service.
   *
   * @param EntityTypeManagerInterface $entity_type_manager
   * The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Add new hook for alter inline block.
   *
   * @param $form
   * @param FormStateInterface $form_state
   * @param $form_id
   */
  public function alterInlineBlockForm(&$form, FormStateInterface $form_state, $form_id) {
    if ($form_id === 'layout_builder_add_block') {
      $storage = $form_state->getStorage();
      $component_config = $storage['layout_builder__component']->get('configuration');
    }
    else {
      $build_info = $form_state->getBuildInfo();
      $block = $build_info['callback_object']->getCurrentComponent();
      $component_config = $block->get('configuration');
    }

    [$plugin_id, $blockId] = explode(':', $component_config['id']);

    if ($plugin_id === 'inline_block') {
      $listOfBlocks = [];
      \Drupal::moduleHandler()->invokeAll('promotion_block_list_alter', [&$listOfBlocks]);

      if (in_array($blockId, $listOfBlocks, true)) {
        $form['settings']['block_form']['#process'][] = [$this, 'addFields'];
        $index = array_search('::submitForm', $form['#submit']);
        $form['#submit'] = array_merge(
          array_slice($form['#submit'], 0, $index),
          [[$this, 'submit_handlers_save_additionaly_fields']],
          array_slice($form['#submit'], $index)
        );
      }
    }
  }

  /**
   * Process callback for inline block forms.
   * Add new fields for block with promotion.
   *
   * @param $form
   * @param FormStateInterface $form_state
   */
  public function addFields(array &$element, FormStateInterface $form_state) {
    self::addFieldCategory($element, $form_state);
    self::addFieldIncludePromo($element, $form_state);
    return $element;
  }

  /**
   * Process callback for inline block forms.
   * Add new field promotion_category.
   *
   * @param $form
   * @param FormStateInterface $form_state
   */
  public function addFieldCategory(array &$element, FormStateInterface $form_state) {
    $component = $form_state->getFormObject()->getCurrentComponent();
    $promotionCategory = $component->get('promotion_category');
    $nodeStorage = $this->entityTypeManager->getStorage('node');
    $element['promotion_category'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'node',
      '#tags' => TRUE,
      '#selection_handler' => 'default',
      '#selection_settings' => [
        'target_bundles' => ['activity'],
      ],
      '#default_value' => $promotionCategory ? $nodeStorage->load($promotionCategory) : '',
      '#description' => t('Reference to Promotion Category from Promotion content type.'),
      '#title' => t('Promotion Category'),
      '#required' => FALSE,
      '#weight' => 20,
    ];
    return $element;
  }

  /**
   * Process callback for inline block forms.
   * Add new field promotion_include.
   *
   * @param $form
   * @param FormStateInterface $form_state
   */
  public function addFieldIncludePromo(array &$element, FormStateInterface $form_state) {
    $component = $form_state->getFormObject()->getCurrentComponent();
    $promotionInclude = $component->get('promotion_include');
    $element['promotion_include'] = [
      '#type' => 'checkbox',
      '#default_value' => $promotionInclude ?? false,
      '#description' => t('If checked, promotion content will replace the data of the block.'),
      '#title' => t('Promotion Include'),
      '#required' => FALSE,
      '#weight' => 21,
    ];
    return $element;
  }

  /**
   * Submit form for save additionaly fields
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  function submit_handlers_save_additionaly_fields(array &$form, FormStateInterface $form_state) {
    $component = $form_state->getFormObject()->getCurrentComponent();
    $values = $form_state->getValue('settings')['block_form'];
    $component->set('promotion_category', $values['promotion_category'] ? $values['promotion_category']['0']['target_id'] : '');
    $component->set('promotion_include', $values['promotion_include'] ?? '');
  }

}
