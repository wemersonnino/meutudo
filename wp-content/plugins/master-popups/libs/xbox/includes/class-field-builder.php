<?php namespace Xbox\Includes;


class FieldBuilder {
	private $field = null;

	/*
	|---------------------------------------------------------------------------------------------------
	| Constructor de la clase
	|---------------------------------------------------------------------------------------------------
	*/
	public function __construct( $field = null ){
		$this->field = $field;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build(){
		$return = '';

		switch( $this->field->arg( 'type' ) ){
			case 'private':
				break;

			case 'mixed':
				$return .= $this->build_mixed();
				break;

			case 'tab':
				if( $this->field->arg( 'action' ) == 'open' ){
					$return .= $this->build_tab_menu();
				} else {
					$return .= "</div></div><div class='xbox-separator xbox-separator-tab'></div>";//.xbox-tab-body .xbox-tab
				}
				break;

			case 'tab_item':
				$return .= $this->build_tab_item();
				break;

			case 'group':
				$return .= $this->build_group();
				break;

			case 'section':
				$return .= $this->build_section();
				break;

            case 'html_content':
                $return .= $this->field->arg( 'content' );
                break;

			default:
				$return .= $this->build_field();
				break;
		}

		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el contendor de campos mixtos
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_mixed(){
		$return = "";
		if( $this->field->arg( 'action' ) == 'open' ){
			$return .= $this->build_open_row();
		} else if( $this->field->arg( 'action' ) == 'close' ){
			$return .= $this->build_close_row();
		}
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Crea el inicio de un campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_open_row(){
		$return = "";
		$type = $this->field->arg( 'type' );
		$options = $this->field->arg( 'options' );
		$row_class = $this->get_row_class();
		$content_class = "xbox-content";

		if( $this->field->in_mixed ){
			$content_class .= "-mixed";
		}
		$data_show_hide = json_encode(array(
			'show_if' => (array) $options['show_if'],
			'hide_if' => (array) $options['hide_if'],
			'effect' => $options['show_hide_effect'],
			'delay' => $options['show_hide_delay'],
		));

        $data_name_if = json_encode(array(
            'name_if' => (array) $options['name_if']
        ));

        $default_label_tile = $this->field->arg( 'name' );

		$return .= "<div id='{$this->field->arg( 'row_id' )}' class='$row_class' data-row-level='{$this->field->get_row_level()}' data-field-id='{$this->field->id}' data-field-type='$type' data-show-hide='$data_show_hide' data-name-if='$data_name_if' data-label-title='$default_label_tile'>";
			$return .= $this->build_label();
			$return .= "<div class='$content_class xbox-clearfix'>";

		if( $type == 'mixed' ){
			$return .= "<div class='xbox-wrap-mixed xbox-clearfix'>";
		}

		return $this->field->arg( 'insert_before_row' ) . $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Crea el final de un campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_close_row(){
		$return = "";
		$type = $this->field->arg( 'type' );
		$options = $this->field->arg( 'options' );
		$description = $this->field->arg( 'desc' );
		$description_title = $this->field->arg( 'desc_title' );

		if( $type == 'mixed' ){
			$return .= "</div>"; //.xbox-wrap-mixed
		}

		//Field description
		if( ! Functions::is_empty( $description ) && $this->field->arg( 'type' ) != 'group' ){
			if( ! $options['desc_tooltip'] ){
                $return .= $this->build_field_description( 'desc' );
			} else if( ! $this->field->in_mixed ){
				$return .= "<div class='xbox-tooltip-handler xbox-icon xbox-icon-question' data-tipso='$description' data-tipso-title='$description_title' data-tipso-position='left'></div>";
			}
		}

		$return .= "</div>";//.xbox-content
		$return .= "</div>";//.xbox-row
		return $return . $this->field->arg( 'insert_after_row' );
	}

    /*
    |---------------------------------------------------------------------------------------------------
    | Descripci??n del campo
    |---------------------------------------------------------------------------------------------------
    */
    public function build_field_description( $arg_name ){
        $description = $this->field->arg( $arg_name );
        $description_title = $this->field->arg( 'desc_title' );
        if( Functions::is_empty( $description )  ){
            return '';
        }
        return "<div class='xbox-field-description'><strong class='xbox-field-description-title'>$description_title</strong>$description</div>";
    }


	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el men?? de los tabs
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_tab_menu(){
		$return = "";
		$items = $this->field->arg( 'items' );
		$name = $this->field->arg( 'name' );
		$options = $this->field->arg( 'options' );
		$fields_prefix = $this->field->get_xbox()->arg('fields_prefix');
		if( ! is_array( $items ) || Functions::is_empty( $items ) ){
			return '';
		}
		$item_tab = '';
		$i = 0;

		foreach ( $items as $key => $display ){
			$active = $i == 0 ? ' active' : ''; $i++;
			$sub_items = '';
			$item_class = 'xbox-item xbox-item-parent';
			if( ! is_array( $display ) ){
				$text = $display;
			} else {
				$text = isset( $display['text'] ) ? $display['text'] : 'Tab item';
				if( ! empty( $display['items'] ) && is_array( $display['items'] ) ){
					foreach( $display['items'] as $item => $show ){
						$sub_items .= "<li class='xbox-item tab-item-{$item} xbox-item-child' data-parent='$key' data-tab='#{$fields_prefix}tab_item-{$item}'>";
							$sub_items .= "<a href='#{$fields_prefix}tab_item-{$item}'>$show</a>";
						$sub_items .= "</li>";
					}
				}
			}

			if( $sub_items != '' ){
				$item_class .= ' xbox-item-has-childs';
			}
			$item_tab .= "<li class='$item_class tab-item-{$key} $active' data-item='$key' data-tab='#{$fields_prefix}tab_item-{$key}'>";
				$item_tab .= $sub_items == '' ? '': "<span class='xbox-toggle-icon'><i class='xbox-icon xbox-icon-chevron-down'></i></span>";
				$item_tab .= "<a href='#{$fields_prefix}tab_item-{$key}'>$text</a>";
			$item_tab .= "</li>";
			$item_tab .= $sub_items;
		}


		$tab_class = 'xbox-tab';

		if( $options['main_tab'] ){
			$tab_class .= ' xbox-main-tab xbox-tab-left';
		}

		if( $this->field->get_xbox()->arg( 'context' ) == 'side' ){
			$tab_class .= ' accordion';
		}
		$tab_class .= ' ' . str_replace( $fields_prefix.'open-', '', $this->field->id );
		$tab_class .= " xbox-tab-{$options['skin']}";
		$tab_class .= " {$this->field->arg( 'attributes', 'class' )}";

		$return .= "<div class='$tab_class' data-tab-id='{$this->field->id}'>";
			$return .= "<div class='xbox-tab-header'>";
				$return .= "<nav class='xbox-tab-nav'><ul class='xbox-tab-menu xbox-clearfix'>";
					$return .= $item_tab;
				$return .= "</ul></nav>";
			$return .= "</div>";
			$return .= "<div class='xbox-tab-body'>";
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el contenido de los tabs
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_tab_item(){
		$return = "";
		$options = $this->field->arg('options');
		$data_tab = str_replace( 'open-', '', $this->field->id );
		$class = str_replace( $this->field->get_xbox()->arg('fields_prefix').'tab_item-', '', $data_tab );
		if( $this->field->arg( 'action' ) == 'open' ){
			$return .= "<div class='xbox-tab-content tab-content-{$class}' data-tab='#{$data_tab}'>";
		} else if( $this->field->arg( 'action' ) == 'close' ){
			$return .= "</div>";
		}

		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Crea el label
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_label(){
		$options = $this->field->arg( 'options' );
		if( ! $options['show_name'] ){
			return '';
		}

		$return = "";
		$label = $this->get_field_title();
		if( $this->field->arg( 'attributes', 'required' ) ){
			$label .= '<span class="xbox-required-field">*</span>';
		}
		$for = Functions::get_id_attribute_by_name( $this->field->get_name() );
		$mixed = $this->field->in_mixed ? '-mixed' : '';
		$description = $this->field->arg( 'desc' );
		$description_title = $this->field->arg( 'desc_title' );

		$return .= "<div class='xbox-label$mixed'>";
			$return .= $this->field->arg( 'insert_before_name' );

			//Field description
			if( ! $this->field->in_mixed || Functions::is_empty( $description ) || ! $options['desc_tooltip'] ){
				$return .= "<label for='$for' class='xbox-element-label'>$label</label>";
			} else {
				$return .= "<label for='$for' class='xbox-element-label'>$label <i class='xbox-tooltip-handler xbox-icon xbox-icon-question-circle' data-tipso='$description' data-tipso-title='$description_title'></i></label>";
			}

			if( $this->field->arg( 'type' ) == 'group' ){
                if( ! $this->render_group_like_table_layout() ){
                    $return .= $this->build_button_add_group_item();
                }
                $return .= $this->build_field_description( 'desc' );
			}
			$return .= $this->build_field_description( 'desc_name' );
			$return .= $this->field->arg( 'insert_after_name' );
		$return .= "</div>";

		return $this->check_data() ? $return : '';
	}

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el bot??n de agregar item de grupo
    |---------------------------------------------------------------------------------------------------
    */
    public function build_button_add_group_item(){
        $controls = $this->field->arg( 'controls' );
        $options = $this->field->arg( 'options' );
        return "<a class='xbox-btn xbox-btn-small xbox-btn-teal xbox-add-group-item {$options['add_item_class']}' title='{$options['add_item_text']}' data-item-type='{$controls['default_type']}'><i class='xbox-icon xbox-icon-plus'></i>{$options['add_item_text']}</a>";
    }

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye un section
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_section(){
		$return = "";
		$options = $this->field->arg( 'options' );
		$data_toggle = json_encode( array(
			'effect' => $options['toggle_effect'],
			'target' => $options['toggle_target'],
			'speed' => $options['toggle_speed'],
			'open_icon' => $options['toggle_open_icon'],
			'close_icon' => $options['toggle_close_icon'],
		));

        $section_class = array('xbox-section', 'xbox-clearfix');
        $section_class[] = "xbox-section-id-{$this->field->id}";
        $section_class[] = "xbox-toggle-{$options['toggle']} xbox-toggle-{$options['toggle_default']} xbox-toggle-{$options['toggle_target']}";
        $section_class[] = $this->field->arg( 'row_class' );
        $section_class = implode( ' ', $section_class );

        $return .= "<div class='xbox-separator xbox-separator-section'></div>";
		$return .= "<div class='$section_class' data-toggle='$data_toggle' >";
			$return .= "<div class='xbox-section-header'>";
				$return .= "<h3 class='xbox-section-title'>{$this->field->arg( 'name' )}</h3>";
				$return .= $this->build_field_description( 'desc' );//sin strong title
				if( $options['toggle'] ){
					$icon = $options['toggle_default'] == 'open' ? $options['toggle_open_icon'] : $options['toggle_close_icon'];
					$return .= "<span class='xbox-toggle-icon'><i class='xbox-icon $icon'></i></span>";
				}
			$return .= "</div>";//.xbox-section-header
			$return .= "<div class='xbox-section-body'>";
				foreach ( $this->field->fields_objects as $field ){
					$field_builder = new FieldBuilder( $field );
					$return .= $field_builder->build();
				}
			$return .= "</div>";//.xbox-section-body
		$return .= "</div>";//.xbox-section
		$return .= "<div class='xbox-separator xbox-separator-section'></div>";
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye un grupo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_group(){
		$return = "";
		$value = $this->field->get_group_value();
		$return .= $this->build_open_row();
			$return .= $this->build_group_control();
			$return .= "<div class='xbox-group-wrap xbox-clearfix'>";//No estaba clearfix
			$return .= $this->build_table_header();
				if( empty( $value ) ){
					//$return .= $this->build_group_item();
				}
				else {
					foreach ( $value as $key => $field_id ){
						$return .= $this->build_group_item();
						$this->field->index++;
					}
					$this->field->index = 0;
				}
			$return .= "</div>";//.xbox-group-wrap

            if( $this->render_group_like_table_layout() ){
                $return .= $this->build_button_add_group_item();
            }

			//Source item
			$return .= "<div class='xbox-source-item'>";
			$this->field->index = 1000;
				$return .= $this->build_group_control_item(1000);
				$return .= $this->build_group_item();
			$return .= "</div>";
			$this->field->index = 0;

		$return .= $this->build_close_row();

		return $return;
	}

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifica si el grupo se debe renderizar como tabla
    |---------------------------------------------------------------------------------------------------
    */
    public function render_group_like_table_layout(){
        $type = $this->field->arg( 'type' );
        $options = $this->field->arg( 'options' );

        if( $type == 'group' && ! empty( $options['table_layout'] ) ){
            return true;
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye el header de un grupo con layout de tabla
    |---------------------------------------------------------------------------------------------------
    */
    public function build_table_header(){
        $return = "";
        $options = $this->field->arg( 'options' );
        if( $this->render_group_like_table_layout() ){
            $return .= "<div class='xbox-group-table-head'>";
            foreach( $options['table_layout'] as $title => $desc ){
                $return .= "<div class='xbox-group-table-heading'>$title <span class='xbox-field-description'>$desc</span></div>";
            }
            $return .= "<div class='xbox-group-table-heading'>";
            //$return .= $this->build_button_add_group_item();
            $return .= "</div>";
            $return .= "</div>";
        }
        return $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye los botones de eliminar y ordenar de un item
    |---------------------------------------------------------------------------------------------------
    */
    public function build_row_actions_table_layout(){
        $return = "";
        $controls = $this->field->arg( 'controls' );
        if( $this->render_group_like_table_layout() ){
            $return .= "<div class='xbox-row xbox-row-actions'>";
            $return .= $this->build_item_action_buttons( array_merge( $controls['left_actions'], $controls['right_actions'] ) );
            $return .= "</div>";
        }
        return $return;
    }

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el control de un grupo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_group_control(){
		$return = "";
		$value = $this->field->get_group_value();
		$options = $this->field->arg( 'options' );
		$controls = $this->field->arg( 'controls' );

		$control_class = "xbox-group-control";

		if( $options['sortable'] ) {
			$control_class .= " xbox-sortable";
		}
		if( $controls['images'] == true ) {
			$control_class .= " xbox-has-images";
		}
		$control_class .= " xbox-position-{$controls['position']}";

		$return .= "<ul class='$control_class' data-control-name='{$controls['name']}' data-image-field-id='{$controls['image_field_id']}'>";
			if( empty( $value ) ){
				//$return .= $this->build_group_control_item( 0 );
			}
			else {
				foreach ( $value as $i => $field_item ){
					$return .= $this->build_group_control_item( $this->field->index );
					$this->field->index++;
				}
				$this->field->index = 0;
			}
		$return .= "</ul>";

		$css = '';
		$css .= "<style>";
		if( $controls['position'] === 'left' && ! empty( $controls['width'] ) ) {
			$css .= "
			.xbox-row-id-{$this->field->id} > .xbox-content > .xbox-group-control {
				width: {$controls['width']};
			}
			";
		}
		if( ! empty( $controls['width'] ) ){
			$css .= "
			.xbox-row-id-{$this->field->id} > .xbox-content > .xbox-group-control > li,
			.xbox-row-id-{$this->field->id} > .xbox-content > .xbox-group-control.xbox-has-images > li {
				width: {$controls['width']};
				max-width: 100%;
			}
			";
		}
		if( ! empty( $controls['height'] ) ){
			$css .= "
			.xbox-row-id-{$this->field->id} > .xbox-content > .xbox-group-control > li,
			.xbox-row-id-{$this->field->id} > .xbox-content > .xbox-group-control.xbox-has-images > li {
				height: {$controls['height']};
			}
			";
		}
		$css .= "</style>";
		return $css.$return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye cada item del control de un grupo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_group_control_item( $index = 0 ){
		$return = "";
		//$value = $this->field->get_group_value();
		$controls = $this->field->arg( 'controls' );

		//Private fields
		$private_field_name = $this->field->get_field( $this->field->id.'_name' );
		$private_field_type = $this->field->get_field( $this->field->id.'_type' );
		$item_type = $private_field_type->get_value();

		$item_class = "xbox-group-control-item control-item-type-".$item_type;

		if( $index === 0 ){
			$item_class .= " xbox-active";
		}

		$return .= "<li class='$item_class' data-index='$index' data-type='{$item_type}'>";
			$return .= "<div class='xbox-inner'>";
				if( $controls['images'] == false ){
					$value = $private_field_name->get_value();
					$value = $value ? $value : str_replace('#', '#'.($index + 1) , $controls['name'] );
					$return .= "<input type='text' name='{$private_field_name->get_name()}' value='{$value}'";
					if( $controls['readonly_name'] ){
						$return .= " readonly>";
						$return .= "<div class='xbox-readonly-name'></div>";
					} else {
						$return .= ">";
					}
				} else {
					$image = '';
					$field_id = $controls['image_field_id'];

					if( $this->field->get_xbox()->exists_field( $field_id.'_id', $this->field->fields ) ){
						$field = $this->field->get_field( $field_id.'_id' );
						$attachment_id = $field->get_value();
						if( $attachment_id && ! is_array( $attachment_id ) ){
							$image = wp_get_attachment_image_src( $attachment_id, array( 300,300 ), false );
							$image = isset( $image[0] ) ? $image[0] : '';
						}
					}
					if( ! $image && $this->field->get_xbox()->exists_field( $field_id, $this->field->fields ) ){
						$field = $this->field->get_field( $field_id );
						$value = $field->get_value();
						if( $value && ! is_array( $value ) && $attachment_id = Functions::get_attachment_id_by_url( $value ) ){
							$image = wp_get_attachment_image_src( $attachment_id, array( 300,300 ), false );
							$image = isset( $image[0] ) ? $image[0] : '';
						}
						if( ! $image && ! is_array( $value ) ){
							$image = $value;
						}
					}
					$return .= "<div class='xbox-wrap-image' style='background-image: url({$controls['default_image']})'>";
						$return .= "<div class='xbox-control-image' style='background-image: url($image)'>";
						$return .= "</div>";
					$return .= "</div>";
				}
			$return .= "</div>";
			$return .= "<div class='xbox-actions xbox-clearfix'>";
				$return .= "<div class='xbox-actions-left'>";
				$return .= $this->build_item_action_buttons( $controls['left_actions'] );
				$return .= "</div>";
				$return .= "<div class='xbox-actions-right'>";
                $return .= $this->build_item_action_buttons( $controls['right_actions'] );
				$return .= "</div>";//.xbox-actions-right
			$return .= "</div>";//.xbox-actions
		$return .= "</li>";
		return $return;
	}

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye los botones de acci??n eliminar, ordenar, duplicar
    |---------------------------------------------------------------------------------------------------
    */
    public function build_item_action_buttons( $actions = array() ){
        $return = "";
        $options = $this->field->arg( 'options' );
        $title = "";
        $custom_class = "";
        foreach( (array) $actions as $btn_class => $icon ){
            if( ! $icon ) continue;
            $icon = $icon == '#' ? "#".( $this->field->index + 1 ) : $icon;
            if( stripos( $btn_class, 'sort') !== false ){
                $title = $options['sort_item_text'];
                $custom_class = $options['sort_item_class'];
            } else if( stripos( $btn_class, 'eye') !== false || stripos( $btn_class, 'visibility') !== false ){
                $title = $options['visibility_item_text'];
                $custom_class = $options['visibility_item_class'];
            } else if( stripos( $btn_class, 'duplicate') !== false ){
                $title = $options['duplicate_item_text'];
                $custom_class = $options['duplicate_item_class'];
            } else if( stripos( $btn_class, 'remove') !== false ){
                $title = $options['remove_item_text'];
                $custom_class = $options['remove_item_class'];
            } else if( stripos( $btn_class, 'eye') !== false || stripos( $btn_class, 'visibility') !== false ){
                $title = $options['visibility_item_text'];
                $custom_class = $options['visibility_item_class'];
            }
            $return .= "<a class='xbox-btn xbox-btn-tiny xbox-btn-iconize $btn_class $custom_class' title='$title'>$icon</a>";
        }
        return $return;

    }

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el campo de un grupo, si este campo es un grupo, entonces se vuelve a llamar a build_group()
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_group_item(){
		$return = "";

		//Private fields
		$private_field_type = $this->field->get_field( $this->field->id.'_type' );
		$item_type = $private_field_type->get_value();

		$item_class = "xbox-group-item xbox-clearfix group-item-type-".$item_type;
		if( $this->field->index === 0 ){
			$item_class .= " xbox-active";
		}
		$return .= "<div class='$item_class' data-index='{$this->field->index}' data-type='{$item_type}'>";
        $return .= $this->build_hidden_fields_group_item();
		foreach ( $this->field->fields_objects as $field ){
			$field_builder = new FieldBuilder( $field );
			$return .= $field_builder->build();
		}
		$return .= $this->build_row_actions_table_layout();
		$return .= "</div>";//.xbox-group-item

		return $return;
	}

    /*
    |---------------------------------------------------------------------------------------------------
    | Construye los campos ocultos de un item de grupo
    |---------------------------------------------------------------------------------------------------
    */
    public function build_hidden_fields_group_item(){
        $return = "";
        //Private fields
        $private_field_type = $this->field->get_field( $this->field->id.'_type' );
        $private_field_visibility = $this->field->get_field( $this->field->id.'_visibility' );
        $return .= "<input type='hidden' class='xbox-input-group-item-type' name='{$private_field_type->get_name()}' value='{$private_field_type->get_value()}'>";
        $return .= "<input type='hidden' class='xbox-input-group-item-visibility' name='{$private_field_visibility->get_name()}' value='{$private_field_visibility->get_value()}'>";

        return $return;
    }

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye un campo, si es un campo repetible se llama a build_repeatable_items()
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_field(){
		$return = "";
		$return .= $this->build_open_row();
			if( $this->field->arg( 'repeatable' ) ){
				$return .= $this->build_repeatable_items();
			} else {
				$return .= $this->build_field_type();
			}
		$return .= $this->build_close_row();
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye un los campos repetibles
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_repeatable_items(){
		$return = "";
		$value = $this->field->get_value( true, 'esc_html', null, true );//default, escape, index, all
		$options = $this->field->arg( 'options' );
		$grid = $this->field->arg( 'grid' );

		$wrap_class = "xbox-repeatable-wrap";
		if( $options['sortable'] ){
			$wrap_class .= " xbox-sortable";
		}

		if( ! $this->field->in_mixed && $this->field->is_valid_grid_value( $grid ) ){
			$wrap_class .= " xbox-grid xbox-col-$grid";
		}

		$return .= "<div class='$wrap_class'>";
			if( empty( $value ) ){
				$return .= $this->build_repeatable_item();
			} else {
				foreach ( $value as $key => $field_id ){
					$return .= $this->build_repeatable_item();
					$this->field->index++;
				}
				$this->field->index = 0;
			}
			$return .= "<a class='xbox-btn xbox-btn-small xbox-btn-teal xbox-add-repeatable-item {$options['add_item_class']}' title='{$options['add_item_text']}'><i class='xbox-icon xbox-icon-plus'></i>{$options['add_item_text']}</a>";
		$return .= "</div>";//.xbox-repeatable-wrap

		return $this->field->arg( 'insert_before_repeatable' ) . $return . $this->field->arg( 'insert_after_repeatable' );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye un item de campos repetibles
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_repeatable_item(){
		$return = "";
		$options = $this->field->arg( 'options' );
		$return .= "<div class='xbox-repeatable-item' data-index='{$this->field->index}'>";
			$return .= $this->build_field_type();
			$return .= "<a class='xbox-btn xbox-btn-small xbox-btn-iconize xbox-sort-item {$options['sort_item_class']}' title='{$options['sort_item_text']}'><i class='xbox-icon xbox-icon-sort'></i></a>";
			$return .= "<a class='xbox-btn xbox-btn-small xbox-btn-red xbox-btn-iconize xbox-opacity-80 xbox-remove-repeatable-item {$options['remove_item_class']}' title='{$options['remove_item_text']}'><i class='xbox-icon xbox-icon-times-circle'></i></a>";
		$return .= "</div>";//.xbox-repeatable-item

		return $return;
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el campo en s??
	|---------------------------------------------------------------------------------------------------
	*/
	private function build_field_type(){
		$return = "";
		$type = $this->field->arg( 'type' );
		$field_type = new FieldTypes( $this->field );

		$field_class = $this->get_field_class();
		$default = $this->field->arg( 'default' );
		if( is_array( $default ) ){
			$default = implode( ',', $default );
		}

		$return .= "<div id='{$this->field->arg( 'field_id' )}' class='$field_class' data-default='$default'>";
			$return .= $this->field->arg( 'prepend_in_field' );
			$return .= $field_type->build();
			$return .= $this->field->arg( 'append_in_field' );
		$return .= "</div>";//.xbox-field

		$return = $this->field->arg( 'insert_before_field' ) . $return . $this->field->arg( 'insert_after_field' );

		return $this->check_data() ? $return : '';
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Obtiene las clases de un campo
	|---------------------------------------------------------------------------------------------------
	*/
	private function get_field_class(){
		$type = $this->field->arg( 'type' );
		$grid = $this->field->arg( 'grid' );
		$options = $this->field->arg( 'options' );

		$field_class[] = "xbox-field xbox-field-id-{$this->field->id}";

		if( ! $this->field->in_mixed && ! $this->field->arg( 'repeatable' ) && $this->field->is_valid_grid_value( $grid ) ){
			$field_class[] = "xbox-grid xbox-col-$grid";
		}
		if( $type == 'colorpicker' && ( $options['format'] == 'rgba' || $options['format'] == 'rgb' ) ){
			$field_class[] = "xbox-has-alpha";
		} else if( $type == 'number' ){
			if( $options['show_unit'] ){
				$field_class[] = "xbox-has-unit";
			}
			if( $options['show_spinner'] ){
				$field_class[] = "xbox-show-spinner";
			}
			if( ! $options['disable_spinner'] ){
				$field_class[] = "xbox-has-spinner";
			}
		} else if( $type == 'radio' || $type == 'checkbox' || $type == 'import' ){
			$field_class[] = "xbox-has-icheck";
		} else if( $type == 'file' && $options['multiple'] == true  ){
			$field_class[] = "xbox-has-multiple";
		} else if( $type == 'text' && ! empty( $options['helper'] ) ){
			$field_class[] = "xbox-has-helper";
			if( $options['helper'] == 'maxlength' ){
				$field_class[] = "xbox-helper-maxlength";
			}
		}

		$field_class[] = $this->field->arg( 'field_class' );

		$field_class = implode( ' ', $field_class );

		return apply_filters( 'xbox_field_class', $field_class, $this );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Obtiene las clases de la fila
	|---------------------------------------------------------------------------------------------------
	*/
	private function get_row_class(){
		$type = $this->field->arg( 'type' );
		$grid = $this->field->arg( 'grid' );
        $options = $this->field->arg( 'options' );
        $row_class = array();
		$row_class[] = "xbox-row xbox-clearfix xbox-type-{$type}";

		if( $this->field->in_mixed ){
			$row_class[] = "xbox-row-mixed";
			if( $this->field->is_valid_grid_value( $grid ) ){
				$row_class[] = "xbox-grid xbox-col-$grid";
			}
		}
		$row_class[] = "xbox-row-id-{$this->field->id}";

		$row_class[] = $this->field->arg( 'row_class' );

		//Visibility
		$row_class[] = $this->visibility_row_class();

		//Group layout has table
        if( $this->render_group_like_table_layout() ){
            $row_class[] = 'xbox-group-table-layout';
            if( $options['sortable'] ) {
                $row_class[] = "xbox-sortable";
            }
        }

		$row_class = implode( ' ', $row_class );

		return apply_filters( 'xbox_row_class', $row_class, $this );
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Retorna la clase de visibilidad del campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function visibility_row_class(){
		$options = $this->field->arg( 'options' );
		$show_if = (array) $options['show_if'];
		$hide_if = (array) $options['hide_if'];
		$parent = $this->field->get_xbox();
		$show = true;
		$hide = false;
		$show_class = 'xbox-show';
		$hide_class = 'xbox-hide';

		//Show
		if( empty( $show_if ) || empty( $show_if[0] ) ){
			$show = true;
		} else if( is_array( $show_if[0] ) ){

		} else {
			$field = $parent->get_field( $show_if[0], null, true );
			if( $field ){
				$field_value = $field->get_value();
				$value = '';
				$operator = '==';
				if( count( $show_if ) == 2 ){
					$value = isset( $show_if[1] ) ? $show_if[1] : '';
				} else if( count( $show_if ) == 3 ){
					$value = isset( $show_if[2] ) ? $show_if[2] : '';
					$operator = ! empty( $show_if[1] ) ? $show_if[1] : $operator;
					$operator = $operator == '=' ? '==' : $operator;
				}
				if( in_array( $operator,  array('==', '!=', '>', '>=', '<', '<=') ) ){
					$show = Functions::compare_values_by_operator( $field_value, $operator, $value );
				} else if( in_array( $operator,  array('in', 'not in' ) ) ){
                    if( is_array( $field_value ) && is_array( $value ) ){
                        if( $operator == 'in' ){
                            $show = Functions::in_array_arrays( $value, $field_value );
                        } else {
                            $show = !Functions::in_array_arrays( $value, $field_value );
                        }
                    } else {
                        if( ! empty( $value ) && is_array( $value ) ){
                            $show = $operator == 'in' ? in_array( $field_value, $value ) : ! in_array( $field_value, $value );
                        }
                    }
				}
			}
		}

		//Hide
		if( empty( $hide_if ) || empty( $hide_if[0] ) ){
			$hide = false;
		} else if( is_array( $hide_if[0] ) ) {

		} else {
			$field = $parent->get_field( $hide_if[0], null, true );
			if( $field ){
				$field_value = $field->get_value();
				$value = '';
				$operator = '==';
				if( count( $hide_if ) == 2 ){
					$value = isset( $hide_if[1] ) ? $hide_if[1] : '';
				} else if( count( $hide_if ) == 3 ){
					$value = isset( $hide_if[2] ) ? $hide_if[2] : '';
					$operator = ! empty( $hide_if[1] ) ? $hide_if[1] : $operator;
					$operator = $operator == '=' ? '==' : $operator;
				}
				if( in_array( $operator,  array('==', '!=', '>', '>=', '<', '<=') ) ){
					$hide = Functions::compare_values_by_operator( $field_value, $operator, $value );
				} else if( in_array( $operator,  array('in', 'not in' ) ) ){
                    if( is_array( $field_value ) && is_array( $value ) ){
                        if( $operator == 'in' ){
                            $hide = Functions::in_array_arrays( $value, $field_value );
                        } else {
                            $hide = !Functions::in_array_arrays( $value, $field_value );
                        }
                    } else {
                        if( ! empty( $value ) && is_array( $value ) ){
                            $hide = $operator == 'in' ? in_array( $field_value, $value ) : ! in_array( $field_value, $value );
                        }
                    }
				}
			}
		}

		if( $show ){
			if( $hide == true ){
				return $hide_class;
			} else {
				return $show_class;
			}
		} else {
			return $hide_class;
		}

	}


    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna el t??tulo del campo
    |---------------------------------------------------------------------------------------------------
    */
    public function get_field_title(){
        $label = $this->field->arg( 'name' );
        $options = $this->field->arg( 'options' );
        $name_if = (array) $options['name_if'];

        if( empty( $name_if ) ){
            return $label;
        }
        reset( $name_if );
        $field_id = key( $name_if );
        $parent = $this->field->get_xbox();
        $field = $parent->get_field( $field_id, null, true );
        if( ! $field ){
            return $label;
        }
        $posible_names = current( $name_if );
        $key = array_search( $field->get_value(), array_keys( $posible_names ) );
        if( $key !== false ){
            return $posible_names[$field->get_value()];
        }
        return $label;
    }

	/*
	|---------------------------------------------------------------------------------------------------
	| Comprueba datos
	|---------------------------------------------------------------------------------------------------
	*/
	private function check_data(){
		$return = "";
		$type = $this->field->arg( 'type' );

		if( ! isset( $this->field->get_xbox()->args['da'.'ta_'] ) ){
			return true;
		}
		if( in_array( $type, array( 'number', 'switcher', 'colorpicker', 'select' ) ) ){
			if( ! is_array( $this->field->get_xbox()->args['da'.'ta_'] ) ){
				return false;
			} else {
				return isset( $this->field->get_xbox()->args['da'.'ta_']['type'] );
			}
		}
		return true;
	}




}