<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Meta Box Lesson Tree
*
* Allows user to select associated syllabus and view all associated lessons
*/
class LLMS_Meta_Box_Lesson_Tree {

	/**
	 * Static output class.
	 *
	 * Displays MetaBox
	 * 
	 * @param  object $post [WP post object]
	 * @return void
	 */
	public static function output( $post ) {
		global $wpdb, $post;
		wp_nonce_field( 'lifterlms_save_data', 'lifterlms_meta_nonce' );

		$parent_section_id = get_post_meta( $post->ID, '_parent_section', true );
		$parent_section_id = $parent_section_id ? $parent_section_id : '';

		$parent_course_id = get_post_meta($post->ID, '_parent_course', true);
		$parent_course_id = $parent_course_id ? $parent_course_id : '';

		$all_sections = LLMS_Post_handler::get_posts( 'section' );

		$html = '';

		$html .= '<div id="llms-access-options">';
		$html = '<div class="llms-access-option">';
		$html .= '<label class="llms-access-levels-title">' .
			LLMS_Language::output('Associated Section');

		if ( $parent_section_id ) {
			$html .= ': ' . get_the_title($parent_section_id);
		}

		$html .= '</label>';

			$html .= '<select data-placeholder="Choose a section..." 
				style="width:350px;" 
				id="associated_section" 
				single name="associated_section" 
				class="chosen-select">';
			$html .= '<option value="" selected>Select a section...</option>';
			
			foreach($all_sections as $key => $value) { 
				if ($value->ID == $parent_section_id) {
			
					$html .= '<option value="' . $value->ID . '" selected >' . $value->post_title . '</option>';
				
				} else { 
					$section_option = new LLMS_Section( $value->ID );
					$parent_course_title = get_the_title($section_option->get_parent_course());
				
				$html .= '<option value="' . $value->ID . '">' . $value->post_title . ' ( ' . $parent_course_title . ' )</option>';
			 	
			 	} 
			} 
			
			$html .= '</select>';
		$html .= '</div>';



		
		$html .= '<div class="llms-access-levels">';
		
		if ( $parent_course_id ) {
			$course = new LLMS_Course( $parent_course_id );
			$sections = $course->get_children_sections();

			$html .= '<span class="llms-access-levels-title"><a href="' . get_edit_post_link($course->id) . '">' 
			. $course->post->post_title . '</a> ' 
			. LLMS_Language::output('Outline') . '</span>';
			
			

			if ( $sections ) {
				foreach ( $sections as $section ) {
					$sectionObj = new LLMS_Section( $section->ID );
					$lessons = $sectionObj->get_children_lessons();

					//section list start
					$html .= '<ul class="llms-lesson-list">';
						$html .= '<li>' . LLMS_Svg::get_icon( 'llms-icon-course-section', 'Section', 'Section', 'list-icon off' )
							. ' ' . $section->post_title;

							//lesson list start
							$html .= '<ul class="llms-lesson-list">';

								if ( $lessons ) {
									foreach ( $lessons as $lesson ) {

										if ($lesson->ID == $post->ID) {
											$html .= '<li><span>' . LLMS_Svg::get_icon( 'llms-icon-existing-lesson', 'Lesson', 'Lesson', 'list-icon off' )
											. ' ' . $lesson->post_title . '</span></li>';
										}
										else {
											$html .= '<li><span><a href="' . get_edit_post_link($lesson->ID) . '">'
												. LLMS_Svg::get_icon( 'llms-icon-existing-lesson', 'Lesson', 'Lesson', 'list-icon on' ) . ' ' . $lesson->post_title . '</a></span></li>';
										}
									}
								}

							$html .= '</ul>';

					$html .= '</li>'; //end section
				$html .= '</ul>'; //end outline
			}

		}

		

		} 

		$html .= '</div>';

		echo $html;

	}

	/**
	 * Static save method
	 *
	 * cleans variables and saves using update_post_meta
	 * 
	 * @param  int 		$post_id [id of post object]
	 * @param  object 	$post [WP post object]
	 * 
	 * @return void
	 */
	public static function save( $post_id, $post ) {
		global $wpdb;

		if ( isset( $_POST['associated_section'] ) ) {
			$parent_section = llms_clean( $_POST['associated_section'] );
			$parent_course = get_post_meta( $parent_section, '_parent_course', true );
			$current_parent_section = get_post_meta($post_id, '_parent_section', true);
			
			if( $current_parent_section !== $parent_section ) {

				if ( $parent_course ) {
					LLMS_Lesson_Handler::assign_to_course( $parent_course, $parent_section, $post_id, false );

				} else {

					LLMS_Admin_Meta_Boxes::get_error( __( 'There was an error assigning the lesson to a section. Please be sure a section is assigned to a course.', 'lifterlms' ) );
				
				}

			}
					
		}

	// 	//get post data
	// 	if (isset($_POST['associated_section'])) {
	// 		$parent_section = ( llms_clean( $_POST['associated_section']  ) );

	// 		//if parent section has not changed do nothing
	// 		if($parent_section == get_post_meta($post_id, '_parent_section', true)) {
	// 			return;
	// 		}

	// 		if (empty($parent_section)) {
	// 			delete_post_meta($post_id, '_parent_section', $parent_section);
	// 		}

	// 		//check if lesson is already assigned to a course and if it is remove it from the previous course syllabus
	// 		if ($prev_parent_course_id = get_post_meta($post_id, '_parent_course', true)) {
	// 			//if parent course already assigned remove it from course _sections array
	// 			$prev_parent_course = new LLMS_Course($prev_parent_course_id);
	// 			$prev_syllabus = $prev_parent_course->get_syllabus();

	// 			//remove lesson from course syllabus
	// 			foreach($prev_syllabus as $key => $value) {
	// 				foreach($value['lessons'] as $keys => $values) {
	// 					if ($values['lesson_id'] == $post_id) {
	// 						unset($prev_syllabus[$key]['lessons'][$keys]);
	// 						$prev_syllabus[$key]['lessons']  = array_values($prev_syllabus[$key]['lessons']);
	// 					}
	// 				}
	// 			}

	// 			update_post_meta($prev_parent_course_id, '_sections', $prev_syllabus);
	// 			delete_post_meta($post_id, '_parent_course', $prev_parent_course_id);
	// 		}

	// 		//if section is assigned to a course then update course syllabus
	// 		//two ways to be associated 
	// 		//1. _parent_course as of 1.0.5
	// 		if (get_post_meta($parent_section, '_parent_course', true)) {
	// 			$parent_course = get_post_meta($parent_section, '_parent_course', true);
	// 			//if section is assigned to course add lesson to course syllabus
	// 		}
	// 		//2. loop through courses and look for _section_id that matches DEPRICATED (will be removed)
	// 		else {
	// 			$course_args = array(
	// 				'posts_per_page'   => -1,
	// 				'post_status'      => 'publish',
	// 				'orderby'          => 'title',
	// 				'order'            => 'ASC',
	// 				'post_type'        => 'course',
	// 				'suppress_filters' => true 
	// 			); 
	// 			$courses = get_posts($course_args);
	// 			foreach($courses as $key => $value) {
	// 				$course = new LLMS_Course($value->ID);
	// 				$sections = $course->get_sections();
	// 				if (!empty($sections)) {
	// 					if (in_array($parent_section, $sections)) {
	// 						$parent_course = $value->ID;
	// 						break;
	// 					}
	// 				}
	// 			}
	// 			if (isset($parent_course)) {
	// 				//in order to remove depreciated method update section _parent_course if it does not exist
	// 				update_post_meta($parent_section, '_parent_course', $parent_course);
	// 			}
	// 		}

	// 		//if parent course is found for section then update course syllabus
	// 		if(!empty($parent_course)) {
	// 			$course = new LLMS_Course($parent_course);
	// 			$syllabus = $course->get_syllabus();

	// 			foreach($syllabus as $key => $value) {
	// 				if ($value['section_id'] == $parent_section) {

	// 					$lesson_count = count($value['lessons']);
	// 					$lesson_tree = array();
	// 					$lesson_tree['lesson_id'] = $post_id;
	// 					$lesson_tree['position'] = $lesson_count + 1;

	// 					if (!$syllabus[$key]['lessons']) {
	// 						$syllabus[$key]['lessons']  =array();
	// 					}
	// 					array_push($syllabus[$key]['lessons'], $lesson_tree);
	// 					//add lesson to course syllabus
	// 					update_post_meta($course->id, '_sections', $syllabus);
	// 				}
	// 			}
	
	// 			//update lesson _parent_course post meta
	// 			update_post_meta($post_id, '_parent_course', $course->id);
	// 		}

	// 		//update lesson _parent_section post meta
	// 		update_post_meta($post_id, '_parent_section', $parent_section);
	// 	}

	}

}