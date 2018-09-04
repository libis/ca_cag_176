<?php
/* ----------------------------------------------------------------------
 * app/templates/checklist.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2014-2015 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name PDF (CAG)
 * @type page
 * @pageSize a4
 * @pageOrientation portrait
 * @tables ca_objects
 *
 * @marginTop 0.75in
 * @marginLeft 0.25in
 * @marginBottom 0.5in
 * @marginRight 0.25in
 *
 * ----------------------------------------------------------------------
 */

    require_once(__CA_APP_DIR__."/helpers/imageHelpers.php");

	$t_display				= $this->getVar('t_display');
	$va_display_list 		= $this->getVar('display_list');
	$vo_result 				= $this->getVar('result');
	$vn_items_per_page 		= $this->getVar('current_items_per_page');
	$vs_current_sort 		= $this->getVar('current_sort');
	$vs_default_action		= $this->getVar('default_action');
	$vo_ar					= $this->getVar('access_restrictions');
	$vo_result_context 		= $this->getVar('result_context');
	$vn_num_items			= (int)$vo_result->numHits();
	
	$vn_start 				= 0;

	print $this->render("pdfStart.php");
    	//libis_start
    	// in order to use header on each page with wkhtmltopdf tool, we need to provide a separate header html file, in app/lib/ca/BaseFindController.php.
	if($this->getVar('PDFRenderer') != "wkhtmltopdf")
        	print $this->render("header.php");
	    //libis_end
	//print $this->render("footer.php"); /*Footer will be added by wkthmltopdf tool in pdf worker*/
?>
		<div id='body'>
<?php
		$vo_result->seek(0);
		
		$vn_line_count = 0;
		while($vo_result->nextHit()) {
			$vn_object_id = $vo_result->get('ca_objects.object_id');		
?>
			<div class="row">
			<table cellpadding="10">
			<tr>
                <td>
                    <?php
                    require_once(__CA_MODELS_DIR__.'/ca_objects.php');
                    $t_object = new ca_objects();
                    $t_object->load($vn_object_id);
                    $imagePids = getImagePids($t_object->get('imageUrl', array('returnAsArray' => true)));
                    if (isset($imagePids) && sizeof($imagePids) > 0){
                        $vs_base_image = getImageThumbnailBase($imagePids[0]);
						print '<div style="overflow: hidden">'.$vs_base_image.'</div>';
                    }
                    else
                        print "<div class=\"imageTinyPlaceholder\">&nbsp;</div>";
                    ?>
                </td>
                <td width="70%">
					<div class="metaBlock">
<?php				
					print "<div class='title'>".$vo_result->getWithTemplate('^ca_objects.preferred_labels.name')."</div>"; 
					foreach($va_display_list as $vn_placement_id => $va_display_item) {
						if (!strlen($vs_display_value = $t_display->getDisplayValue($vo_result, $vn_placement_id, array('forReport' => true, 'purify' => true)))) {
							if (!(bool)$t_display->getSetting('show_empty_values')) { continue; }
							$vs_display_value = "&lt;"._t('not defined')."&gt;";
						} 
						
						print "<div class='metadata'><span class='displayHeader'>".$va_display_item['display']."</span>: <span class='displayValue' >".(strlen($vs_display_value) > 1200 ? strip_tags(substr($vs_display_value, 0, 1197))."..." : $vs_display_value)."</span></div>";		
					}							
?>
					</div>				
				</td>	
			</tr>
			</table>	
			</div>
<?php
		}
?>
		</div>
<?php
	print $this->render("pdfEnd.php");
?>
