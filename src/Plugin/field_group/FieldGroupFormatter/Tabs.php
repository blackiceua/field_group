<?php

namespace Drupal\field_group\Plugin\field_group\FieldGroupFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormState;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\VerticalTabs;
use Drupal\field_group\Element\HorizontalTabs;
use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Plugin implementation of the 'horizontal_tabs' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "tabs",
 *   label = @Translation("Tabs"),
 *   description = @Translation("This fieldgroup renders child groups in its own tabs wrapper."),
 *   supported_contexts = {
 *     "form",
 *     "view",
 *   }
 * )
 */
class Tabs extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function preRender(&$element, $rendering_object) {

    parent::preRender($element, $rendering_object);

    $element += array(
      '#prefix' => '<div class=" ' . implode(' ' , $this->getClasses()) . '">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
      '#parents' => array($this->group->group_name),
      '#default_tab' => '',
    );

    if ($this->getSetting('id')) {
      $element['#id'] = Html::getId($this->getSetting('id'));
    }

    // By default tabs don't have titles but you can override it in the theme.
    if ($this->getLabel()) {
      $element['#title'] = Html::escape($this->getLabel());
    }

    if ($this->getSetting('direction') == 'vertical') {

      $element += array(
        '#type' => 'vertical_tabs',
        '#theme_wrappers' => array('vertical_tabs'),
      );
    }
    else {
      $element += array(
        '#type' => 'horizontal_tabs',
        '#theme_wrappers' => array('horizontal_tabs'),
      );
    }

    // Search for a tab that was marked as open. First one wins.
    foreach (Element::children($element) as $tab_name) {
      if (!empty($element[$tab_name]['#open'])) {
        $element[$this->group->group_name . '__active_tab']['#default_value'] = $tab_name;
        break;
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm() {

    $form = parent::settingsForm();

    $form['direction'] = array(
      '#title' => $this->t('Direction'),
      '#type' => 'select',
      '#options' => array(
        'vertical' => $this->t('Vertical'),
        'horizontal' => $this->t('Horizontal'),
      ),
      '#default_value' => $this->getSetting('direction'),
      '#weight' => 1,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {

    $summary = parent::settingsSummary();
    $summary[] = $this->t('Direction: @direction',
      array('@direction' => $this->getSetting('direction'))
    );

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultContextSettings($context) {
    return array(
      'direction' => 'vertical',
    ) + parent::defaultContextSettings($context);
  }

  /**
   * {@inheritdoc}
   */
  public function getClasses() {

    $classes = parent::getClasses();
    $classes[] = 'field-group-' . $this->group->format_type . '-wrapper';

    return $classes;
  }

}
