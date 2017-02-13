<?php

/**
 * @file
 *
 * Contains \Drupal\path_rest\Controller\PathRestController.
 *
 */

namespace Drupal\path_rest\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PathRestController extends ControllerBase {

  public function get_path(Request $request) {
    $route = $request->query->get('route');
    $route = array('alias' => '/'.$route);
    $router_service = \Drupal::service('path.alias_storage');
    $path_alias = $router_service->load($route);

    if($path_alias !== null) {

      $path_array = array_values(array_filter(explode('/', $path_alias['source'])));
      if(is_numeric((int) $path_array[1]) && isset($path_array[1])) {
        $node = Node::load($path_array[1]);
        $type = $node->get('type')->get(0)->getValue();
        $uuid = $node->get('uuid')->get(0)->getValue();
        $uuid_value = $uuid['value'];
        $type_value = $type['target_id'];
        $redirect_response = new RedirectResponse('/jsonapi/node/'. $type_value . '/' . $uuid_value . '?_format=api_json');
        return $redirect_response;
      }

    } else {
      return new NotFoundHttpException();
    }

  }

}