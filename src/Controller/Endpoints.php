<?php

/**
 * @file
 * Contains Drupal\product_catalog\Controller\Endpoints.
 */

namespace Drupal\product_catalog\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Drupal\user\UserDataInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Endpoint related to the product catalog.
 */
class Endpoints extends ControllerBase {

  /**
   * The user data service.
   * @var UserDataInterface
   */
  private $userData;

  /**
   * Construct a Product Catalog Endpoints object.
   */
  public function __construct() {
    $this->userData = \Drupal::service('user.data');
    $this->currentUser = \Drupal::currentUser();
  }

  /**
   * Callback function to get all the fav products of the current user.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   * With an array of all the fav products as the response content.
   */
  public function get_user_fav_products() {
    $response = new Response();
    $userId = $this->currentUser->id();

    $result['nids'] = $this->userData
      ->get('product_catalog', $userId, 'fav_products') ?? [];

    $response->setContent(Json::encode($result));
    return $response;
  }

  /**
   * Callback function to set a fav product for the current user.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request the request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   * A response with the results of the operation.
   */
  public function set_user_fav_products(Request $request) {
    $response = new Response();
    $data = $request->request->all();

    if(!isset($data['nid']) || !isset($data['op'])) {
      $response->setStatusCode(400);
      return $response;
    }

    $nid = $data['nid'];
    $op = $data['op'];

    $userId = $this->currentUser->id();
    $fav_products = $this->userData
      ->get('product_catalog', $userId, 'fav_products') ?? [];

    $result = '';

    if($op === 'add') {
      if(!in_array($nid, $fav_products)) $fav_products[] = $nid;
      $result = 'added';
    }
    elseif($op === 'remove') {
      $fav_products = array_diff($fav_products, [$nid]);
      $result = 'removed';
    }

    $this->userData->set('product_catalog', $userId, 'fav_products', $fav_products);
    $response->setContent(Json::encode(['result'=>$result, 'nid' => $nid]));
    return $response;
  }

}
