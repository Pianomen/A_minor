<?php abspath_check("ABSPATH"); require_once(vc_path_dir( "SHORTCODES_DIR", "vc-single-image.php" ));

class WPBakeryShortCode_vc_image_checklist extends WPBakeryShortCode {}

  function before_img($letter, $img_alt) {
          $output .= "<label><input type='checkbox' class='img_check'><span class='custom--checkbox'></span><span class='custom--checkbox--letter'><i class='image_key'>" . $letter . "</i><i class='image_alt'>" . $img_alt . "</i></span><span class='label-wrapper'></span>";
          // $out = explode(",",$output);
          return $output;
  }

  function vc_image_checklist_output($atts, $content = null){
    extract(
      shortcode_atts(
        array(
          "el_class" => "",
          "images" => "",
          "image_size" => "full",
          "grid_type" => "2x2",
          "descript" => "",
          "display" => "grid"
        ), $atts
      )
    );

    if(strpos($image_size, "x") > 0):
      $image_size = explode("x", $image_size);
      array_walk($image_size, function($item) {$item .= "px";});
    endif;

    $output = do_shortcode($content);
    $images_ids = $images !== "" ? explode(",",trim($images)) : null;

    if($images_ids):
      // Creating an alphabet array
      $alphabet = range("A","Z"); $j = 0; $hz = "";  // iterators

      // Setting image attributes array
      $img_atts = [
        "class" => "attachment-thumbnail",
        "src"   => vc_asset_url("vc/blank.gif"),
      ];
                                      //class oo-hz_slider    replace to  (oo-slider)
        $output .= "<div class='oo-".$display."'><h4 style='text-transform:none;'>". $descript ."</h4>";


        $ul_class = ($display === "grid" ? "oo_grid-" . esc_attr($grid_type) : "");

        $output .= "<ul class='". $ul_class ."'>";

        $img_group = []; $imgalt_array = [];

        // before_img function here

        foreach ( $images_ids as $image_id ):
          $img = wp_get_attachment_image($image_id, $image_size, $img_atts);
          $img_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);

          $alt_exists = array_search($img_alt, $imgalt_array);

          if($alt_exists === false):
            array_push($imgalt_array, $img_alt);
            array_push($img_group,  before_img($alphabet[$j], $img_alt) . $img);
          else:
            $img_group[$alt_exists] .= $img;
          endif; $j++;

        endforeach;

        array_walk($img_group, function(&$item){
          $item = "<li>{$item}</label></li>";
        });

        if($display === "slider") {
          $hz = "<div class='hz-buttons'><input type='button' class='hz-btn-prev'><input type='button' class='hz-btn-next'></div>";
          array_walk($img_group, function(&$item, $key){
            if($key % 3 === 0 && $key > 0) {
              $item = "</ul><ul>{$item}";
            }
          });
        }

        $output .= join("", $img_group);
        $output .= "</ul>";
        $output .= "{$hz}</div>";
    endif;

    return $output;
  }
  add_shortcode( "vc_image_checklist", "vc_image_checklist_output");




/**/



    function vc_image_checklist_mapping() {
    // Stop all if VC is not enabled
        if ( !defined( 'WPB_VC_VERSION' ) ) {
            return;
        }


        // Map the block with vc_map()
        vc_map(
          array(
            'name' => __('VC Image Checklist', 'bridge-child'),
            'base' => 'vc_image_checklist',
            'description' => __('Description', 'bridge-child'),
            'content_element' => true,
            'show_settings_on_create' => true,
            'category' => __('Custom VC Elements', 'bridge-child'),
            'icon' => get_template_directory_uri() . "/img/picture.png",
            'params' => array(/*

              */array(
                "type" => "textfield",
                "class" => "",
                "heading" => __( "Content Description", "bridge-child" ),
                "param_name" => "descript",
                "description" => __( "Please add description to the checklist", "bridge-child" ),
              ),/*

              */array(
                "type" => "attach_images",
                "class" => "",
                "heading" => __("(You can group the images together by using the same alternative text for each image) </br></br>", "bridge-child " ),
                "param_name" => "images",
                "description" => __( "Selecting images for output", "bridge-child" ),
                'value' => __( "", 'bridge-child' ),
              ),/*

              */array(
                  'type' => 'textfield',
                  'heading' => __('Extra class name', 'bridge-child'),
                  'param_name' => 'el_class',
                  'description' => __('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'bridge-child')
              ),/*

              */array(
                "type" => "textfield",
                "heading" => "Image size",
                "param_name" => "image_size",
                "description" => "set image size",
                ),/**

              */array(
                "type" => "dropdown",
                "class" => "",
                "heading" => __( "Grid Type", "bridge-child" ),
                "param_name" => "grid_type",
                "description" => __( "Select the grid type from available options above", "bridge-child" ),
                "value" => __( ["2x2", "3x3", "4x4", "5x5"], "bridge-child"),
                "std" => "2x2"
              ),/*

              */array(
                "type" => "dropdown",
                "class" => "",
                "heading" => __( "Display", "bridge-child" ),
                "param_name" => "display",
                "description" => __( "Select the display type from available options above", "bridge-child" ),
                "value" => __( ["slider", "grid"], "bridge-child"),
                "std" => "grid"
              ),
            )
          )
        );
     }
add_action( "init", "vc_image_checklist_mapping");
