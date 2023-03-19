<?php

namespace Drupal\product_catalog\Form;

use Drupal\Component\Utility\Environment;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\product_catalog\CSVReader;
use Drupal\product_catalog\Importer;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductImport extends FormBase {

  /**
   * The CSV file reader.
   *
   * @var \Drupal\product_catalog\CSVReader
   */
  protected $file_reader;

  /**
   * The content type of the product catalog
   *
   * @var string
   */
  private string $product_bundle = 'products';

  /**
   * The fields of the product catalog content type
   *
   * @var string[]
   */
  private array $product_fields = [
    'title',
    'body',
    'field_price',
  ];

  /**
   * Construct the ProductImporter form.
   *
   * @param \Drupal\product_catalog\CSVReader $file_reader
   * The file reader service
   */
  public function __construct(CSVReader $file_reader) {
    $this->file_reader = $file_reader;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('product_catalog.file_reader'),
    );
  }


  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'product_catalog_import';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#title'] = $this->t('Product Import');

    $form['delimiter'] = [
      '#type' => 'select',
      '#title' => $this->t('Select delimiter'),
      '#options' => [
        ',' => ',',
        '~' => '~',
        ';' => ';',
        ':' => ':',
      ],
      '#default_value' => ',',
      '#required' => TRUE,
    ];

    $validators = [
      'file_validate_extensions' => ['csv'],
      'file_validate_size' => [Environment::getUploadMaxSize()],
    ];
    $form['attachment'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Import File'),
      '#description' => [
        '#theme' => 'file_upload_help',
        '#description' => $this->t('A product catalog import file.'),
        '#upload_validators' => $validators,
      ],
      '#upload_location' => 'temporary://',
      '#upload_validators' => $validators,
      '#required' => true
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
    ];

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $delimiter = $form_state->getValue('delimiter');
    $fid = $form_state->getValue('attachment');

    ['headers' => $csv_fields, 'data' => $csv_data] = $this->file_reader->getCsvByFid(current($fid), $delimiter);

    //Check for missing fields
    $missing = array_diff($this->product_fields, $csv_fields);
    if ($missing) {
      $this->messenger()->addError(
        $this->t('the CSV is missing the next fields: ' .
          implode(', ', $missing))
      );

      return;
    }

    $product_data = $this->mapData($csv_fields, $csv_data);
    $importer = new Importer($this->product_bundle, $product_data);
    $importer->processNodes();
  }

  /**
   * Transform the csv data into a key-value pair array to create the nodes.
   *
   * @param array $fields the node fields
   * @param array $data the data read from the CSV file.
   *
   * @return array formatted data array
   */
  private function mapData(array $fields, array $data): array {
    return array_map(function ($row) use ($fields) {
      return array_combine($fields, $row);
    }, $data);
  }
}
