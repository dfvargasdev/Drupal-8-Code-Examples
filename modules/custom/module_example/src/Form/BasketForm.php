<?php

namespace Drupal\module_example\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements a sample form with different types of fields.
 */
class BasketForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'basket_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#required' => TRUE,
    ];

    $form['phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone'),
      '#description' => $this->t('Enter a phone number in the format xxx-xxx-xxxx.'),
      '#pattern' => '\d{3}-\d{3}-\d{4}',
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message'),
      '#rows' => 5,
      '#required' => TRUE,
    ];

    $form['quantity'] = [
      '#type' => 'number',
      '#title' => $this->t('Quantity'),
      '#required' => TRUE,
      '#min' => 1,
    ];

    $form['color'] = [
      '#type' => 'select',
      '#title' => $this->t('Color'),
      '#options' => [
        'red' => $this->t('Red'),
        'blue' => $this->t('Blue'),
        'green' => $this->t('Green'),
      ],
      '#empty_option' => $this->t('- Select -'),
      '#required' => TRUE,
    ];

    $form['gender'] = [
      '#type' => 'select',
      '#title' => $this->t('Gender'),
      '#options' => [
        'male' => $this->t('Male'),
        'female' => $this->t('Female'),
        'not' => $this->t('Not'),
      ],
      '#empty_option' => $this->t('- Select -'),
      '#required' => TRUE,
    ];

    $form['interests'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Interests'),
      '#options' => [
        'football' => $this->t('Football'),
        'basketball' => $this->t('Basketball'),
        'tennis' => $this->t('Tennis'),
      ],
    ];

    $form['delivery_date'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Delivery Date'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    if (strlen($form_state->getValue('name')) < 3) {
      $form_state->setErrorByName('name', $this->t('Name must be at least 3 characters long.'));
    }

    if ($form_state->getValue('quantity') <= 0) {
      $form_state->setErrorByName('quantity', $this->t('Quantity must be a positive number.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // Retrieve the values submitted by the user
    $values = $form_state->getValues();

    $timestamp = time();
    $delivery_date = strtotime($form_state->getValue('delivery_date'));

    // Insert data into the custom table
    $database = \Drupal::database();
    $database->insert('custom_form')
      ->fields([
        'name' => $values['name'],
        'email' => $values['email'],
        'phone' => $values['phone'],
        'message' => $values['message'],
        'quantity' => $values['quantity'],
        'color' => $values['color'],
        'gender' => $values['gender'],
        'interests' => implode(',', $values['interests']),
        'delivery_date' => $delivery_date,
        'created' => $timestamp,
      ])
      ->execute();

    // Display a message to the user
    drupal_set_message($this->t('Form submitted successfully.'));

    drupal_set_message($this->t('Thanks for submitting the basket form with the following information: <br>Name: @name <br>Email: @email <br>Phone: @phone <br>Message: @message <br>Quantity: @quantity <br>Color: @color <br>Delivery Date: @delivery_date <br>Delivery Time: @delivery_time <br>Gender: @gender <br>Interests: @interests', [
      '@name' => $values['name'],
      '@email' => $values['email'],
      '@phone' => $values['phone'],
      '@message' => $values['message'],
      '@quantity' => $values['quantity'],
      '@color' => $values['color'],
      '@delivery_date' => $values['delivery_date'],
      '@gender' => $values['gender'],
      '@interests' => implode(',', $values['interests']),
    ]));

  }

}