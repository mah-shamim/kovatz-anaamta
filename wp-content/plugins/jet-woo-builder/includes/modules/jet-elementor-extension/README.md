# Jet Elementor Extension

## Features:

### Query control

The Query control is extendent from Select2 control. Type - `jet-query`.

#### Additional argumens:
 - `query_type` - query type. Options: post, elementor_templates, tax. Default: post.
 - `query` - array of query arguments.
 - `prevent_looping` - prevent looping. Default: false.
 - `edit_button` - add edit button https://prnt.sc/12u4jsv. Default: 
    ```
    array( 
      'active' => false, 
      'label'  => 'Edit Template', 
    )
    ```

#### Usage:

- for elementor templates:
```
$this->add_control(
  'item_template',
  array(
    'label'       => esc_html__( 'Choose Template', 'jet-menu' ),
    'type'        => 'jet-query',
    'query_type'  => 'elementor_templates',
  )
);
```
- for posts:
```
$this->add_control(
  'jet_woo_builder_cart_popup_template',
  array(
    'label'       => esc_html__( 'Popup', 'jet-woo-builder' ),
    'type'        => 'jet-query',
    'query_type'  => 'post',
    'query'       => apply_filters( 'jet_popup_default_query_args', array(
      'post_type'      => jet_popup()->post_type->slug(),
      'order'          => 'DESC',
      'orderby'        => 'date',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
     ) ),
  )
);
```
- with `edit_button`:
```
$this->add_control(
  'lisitng_id',
    array(
      'label'      => __( 'Listing', 'jet-engine' ),
      'type'       => 'jet-query',
      'query_type' => 'post',
      'query'      => array(
        'post_type' => jet_engine()->post_type->slug(),
      ),
      'edit_button' => array(
        'active' => true,
        'label'  => __( 'Edit Listing', 'jet-engine' ),
      ),
  )
);
```
