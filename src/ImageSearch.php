<?php

/**
 * @file Contains the Drupal\product_catalog\ImageSearch class
 */
namespace Drupal\product_catalog;

use Drupal\Component\Serialization\Json;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

/**
 * The ImageSearch service class.
 */
class ImageSearch {

  const JOJ_IMAGE_ENDPOINT = 'https://joj-image-search.p.rapidapi.com/v1/';

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\Client|ClientInterface
   */
  protected Client|ClientInterface $httpClient;

  protected string $api_host;

  protected string $api_key;

  /**
   * Constructs a ImageSearch object
   */
  public function __construct() {
    $this->httpClient = \Drupal::httpClient();

    $config = \Drupal::config('product_catalog.image_search.settings');
    $this->api_host = $config->get('rapid_host');
    $this->api_key = $config->get('rapid_key');
  }

  public function get_image(string $keyword) {

    $options = [
      'headers' => [
        'X-RapidAPI-Key' => $this->api_key,
        'X-RapidAPI-Host' => $this->api_host,
      ],
      'query' => [
        'keyword' => $keyword,
        'max' => '1',
      ],
    ];

    $request = $this->httpClient->get(self::JOJ_IMAGE_ENDPOINT, $options);

    if ($request->getStatusCode() != 200) {
      return null;
    }

    $results = $request->getBody()->getContents();
    $results = Json::decode($results);
    return $results[0]["thumbnails"][0]["url"] ?? $results[0]["image"]["url"] ?? '';
  }
}
