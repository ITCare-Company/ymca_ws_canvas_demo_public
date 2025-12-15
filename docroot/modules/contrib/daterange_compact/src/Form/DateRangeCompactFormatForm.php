<?php

declare(strict_types=1);

namespace Drupal\daterange_compact\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;

/**
 * Provides a form for editing compact date range formats.
 *
 * @package Drupal\daterange_compact\Form
 */
class DateRangeCompactFormatForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\daterange_compact\Entity\DateRangeCompactFormatInterface $format */
    $format = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $format->label(),
      '#description' => $this->t("Name of the format."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $format->id(),
      '#machine_name' => [
        'exists' => '\Drupal\daterange_compact\Entity\DateRangeCompactFormat::load',
      ],
      '#disabled' => !$format->isNew(),
    ];

    $form['default_pattern'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Standard pattern'),
      '#default_value' => $format->get('default_pattern') ?: '',
      '#maxlength' => 100,
      '#description' => $this->t('A user-defined date format. See the <a href="http://php.net/manual/function.date.php">PHP manual</a> for available options.'),
      '#required' => TRUE,
    ];

    $form['default_separator'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Separator'),
      '#default_value' => $format->get('default_separator') ?: '',
      '#maxlength' => 100,
      '#size' => 10,
      '#description' => $this->t('Text between start and end dates.'),
      '#required' => TRUE,
    ];

    $form['formats'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Options'),
      '#tree' => FALSE,
    ];

    $form['formats']['same_day'] = [
      '#type' => 'details',
      '#title' => $this->t('Same day'),
      '#open' => TRUE,
      '#weight' => 2,
      '#group' => 'formats',
      '#description' => $this->t('Optional formatting of time ranges within a single day. Do not use this for date-only formats.'),
    ];

    $form['formats']['same_day']['same_day_start_pattern'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Start date/time pattern'),
      '#default_value' => $format->get('same_day_start_pattern') ?: '',
      '#maxlength' => 100,
      '#description' => $this->t('A user-defined date format. See the <a href="http://php.net/manual/function.date.php">PHP manual</a> for available options.'),
    ];

    $form['formats']['same_day']['same_day_separator'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Separator'),
      '#default_value' => $format->get('same_day_separator') ?: '',
      '#maxlength' => 100,
      '#size' => 10,
      '#description' => $this->t('Text between start and end dates. If left blank, the basic separator is used.'),
      '#required' => FALSE,
    ];

    $form['formats']['same_day']['same_day_end_pattern'] = [
      '#type' => 'textfield',
      '#title' => $this->t('End date/time pattern'),
      '#default_value' => $format->get('same_day_end_pattern') ?: '',
      '#maxlength' => 100,
      '#description' => $this->t('A user-defined date format. See the <a href="http://php.net/manual/function.date.php">PHP manual</a> for available options.'),
    ];

    $form['formats']['same_day']['same_day_omit_duplicate_ampm'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Omit duplicate am/pm where possible.'),
      '#default_value' => $format->get('same_day_omit_duplicate_ampm'),
      '#description' => $this->t('If checked, ranges like <em>9am-10am</em> will become <em>9-10am</em>.'),
    ];

    $form['formats']['same_month'] = [
      '#type' => 'details',
      '#title' => $this->t('Same month'),
      '#open' => TRUE,
      '#weight' => 3,
      '#group' => 'formats',
      '#description' => $this->t('Optional formatting of date ranges that span multiple days within the same month.'),
    ];

    $form['formats']['same_month']['same_month_start_pattern'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Start date pattern'),
      '#default_value' => $format->get('same_month_start_pattern') ?: '',
      '#maxlength' => 100,
      '#description' => $this->t('A user-defined date format. See the <a href="http://php.net/manual/function.date.php">PHP manual</a> for available options.'),
    ];

    $form['formats']['same_month']['same_month_separator'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Separator'),
      '#default_value' => $format->get('same_month_separator') ?: '',
      '#maxlength' => 100,
      '#size' => 10,
      '#description' => $this->t('Text between start and end dates. If left blank, the basic separator is used.'),
      '#required' => FALSE,
    ];

    $form['formats']['same_month']['same_month_end_pattern'] = [
      '#type' => 'textfield',
      '#title' => $this->t('End date pattern'),
      '#default_value' => $format->get('same_month_end_pattern') ?: '',
      '#maxlength' => 100,
      '#description' => $this->t('A user-defined date format. See the <a href="http://php.net/manual/function.date.php">PHP manual</a> for available options.'),
    ];

    $form['formats']['same_year'] = [
      '#type' => 'details',
      '#title' => $this->t('Same year'),
      '#open' => TRUE,
      '#weight' => 4,
      '#group' => 'formats',
      '#description' => $this->t('Optional formatting of date ranges that span multiple months within the same year.'),
    ];

    $form['formats']['same_year']['same_year_start_pattern'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Start date pattern'),
      '#default_value' => $format->get('same_year_start_pattern') ?: '',
      '#maxlength' => 100,
      '#description' => $this->t('A user-defined date format. See the <a href="http://php.net/manual/function.date.php">PHP manual</a> for available options.'),
    ];

    $form['formats']['same_year']['same_year_separator'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Separator'),
      '#default_value' => $format->get('same_year_separator') ?: '',
      '#maxlength' => 100,
      '#size' => 10,
      '#description' => $this->t('Text between start and end dates. If left blank, the basic separator is used.'),
      '#required' => FALSE,
    ];

    $form['formats']['same_year']['same_year_end_pattern'] = [
      '#type' => 'textfield',
      '#title' => $this->t('End date pattern'),
      '#default_value' => $format->get('same_year_end_pattern') ?: '',
      '#maxlength' => 100,
      '#description' => $this->t('A user-defined date format. See the <a href="http://php.net/manual/function.date.php">PHP manual</a> for available options.'),
    ];

    $form['formats']['zero_minutes'] = [
      '#type' => 'details',
      '#title' => $this->t('Omit minutes when zero'),
      '#open' => TRUE,
      '#weight' => 4,
      '#group' => 'formats',
    ];
    $form['formats']['zero_minutes']['zero_minutes_omit'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Omit minutes when zero.'),
      '#default_value' => $format->get('zero_minutes_omit'),
      '#description' => $this->t('Example: <em>9:00am</em> becomes <em>9am</em>.<br>Do not use this with formats that display seconds.'),
    ];
    $form['formats']['zero_minutes']['zero_minutes_omit_pattern'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Minute pattern'),
      '#default_value' => $format->get('zero_minutes_omit_pattern'),
      '#description' => $this->t('Must match the minutes part of the full pattern. To remove <em>:00</em>, use <code>:i</code>'),
      '#required' => TRUE,
      '#states' => [
        'visible' => [
          ':input[name="zero_minutes_omit"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\daterange_compact\Entity\DateRangeCompactFormatInterface $format */
    $format = $this->entity;
    $status = $format->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('Created the %label format.', [
          '%label' => $format->label(),
        ]));
        break;

      default:
        $this->messenger()->addStatus($this->t('Updated the %label format.', [
          '%label' => $format->label(),
        ]));
    }
    $form_state->setRedirectUrl($format->toUrl('collection'));
  }

}
