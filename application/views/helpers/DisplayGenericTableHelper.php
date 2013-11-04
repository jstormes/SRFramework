<?php

class Zend_View_Helper_DisplayGenericTableHelper extends Zend_View_Helper_Abstract {

	public $view;

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}
	
	public function displayGenericTableHelper($data, $req, $showheader=1) {
		
		$table = "";
		$headerRight = "";
		$countcolor = 0;
		$count = count($data);
		$td_count = count($req['alias']);
		
		
		$searchResults = (empty($req['search_type'][0])) ? "" : "<b>Search Type:</b> ".$req['search_type'][0]."<br>";
		$searchResults .= (empty($req['searching'][0])) ? "" : "<b>Searching:</b> ".$req['searching'][0];
		$searchResults .= (empty($req['search_custom'][0])) ? "" : "<b>".$req['search_custom'][0]."</b>";
		
		if(isset($req['export_links'])) {
			foreach ($req['export_links'] as $file_type) {
				$headerRight .= (empty($headerRight)) ? "" : "&nbsp;";
				//$file_path = "images/file_icons/".$file_type."_file.png";
				$file_path = "images/softicons/".$file_type."-16.png";
				$headerRight .= "<input type=\"image\" src=\"".$this->view->baseUrl($file_path)."\" id=\"".$file_type."_export\" title=\"SAVE to ".strtoupper($file_type)."\" style=\"width:auto; height:auto;vertical-align: middle;\">";
			}
			$headerRight .= "</br>";
		}
		
		if ($showheader == 1) {
		$headerRight .= "<b>Total Records:</b> ".$count;
		}
		
		# <TABLE>
		if(count($data)>0) {
			$table .= "<table id=\"reportTable\" name=\"reportTable\" class=\"".$req['class'][0]."\"><tr>";
			
			if(isset($req['td_actions_button']) === true) { $td_count++; }
			
			$table .="<td colspan=".$td_count."><table width=\"100%\"><tr><td class=\"reportTableHeaderLeft\">".$searchResults."</td><td class=\"reportTableHeaderRight\" nowrap>".$headerRight."</td></tr></table></td></tr><tr>";
			
			# Header Cells
			$i = 0;
			foreach($req['alias'] as $alias) {
				$table .= "<th class=\"".$req['class'][1]."\" width=\"".$req['width'][$i]."\">".$alias."</th>";	
				$i++;
			}
			if(isset($req['td_actions_button']) === true) {
			    $table .= "<th class=\"".$req['class'][1]."\" width=\"100\"> ACTIONS </th>";
			}
			$table .= "</tr>";
			
			# TR / TD
			foreach($data as $key => $val) {
				
				# Alternate <TR> Colors
				($countcolor == '0') ? $countcolor='1' : $countcolor='0';
				$tr = "bgcolor='".$req['rowcolor'][$countcolor]."' onMouseOver=\"this.bgColor='".$req['rowcolor'][2]."'\" onMouseOut=\"this.bgColor='".$req['rowcolor'][$countcolor]."'\"";
				
				$pk = ((isset($req['pk']) === true) && (isset($req['pk']) === true)) ? "id=\"".$data[$key][$req['pk'][0]]."\"" : '';
				$table .= "<tr ".$pk." ".$tr.">";
				
				# <TD>
				foreach($req['field'] as $field_key => $field_val) {
				    
				    $td_css = ((isset($req['td_css']) === true) && (isset($req['td_css']) === true)) ? "class=\"".$req['td_css'][$field_key]."\"" : "class=\"reportTableleft\"";
				    $td_id_attr = ((isset($req['td_id_attr']) === true) && (isset($req['td_id_attr']) === true)) ? "id=\"".$req['td_id_attr'][$field_key]."\"" : '';
				    $td_name_attr = ((isset($req['td_name_attr']) === true) && (isset($req['td_name_attr']) === true)) ? "id=\"".$req['td_name_attr'][$field_key]."\"" : '';
				    $td_target_attr = ((isset($req['td_target_attr']) === true) && (isset($req['td_target_attr']) === true)) ? "target=\"".$req['td_target_attr'][$field_key]."\"" : '';
				    $td_title_attr = ((isset($req['td_title_attr']) === true) && (isset($req['td_title_attr']) === true)) ? "title=\"".$req['td_title_attr'][$field_key]."\"" : '';
				    $td_href_anc = ((isset($req['td_href_anc']) === true) && ($req['td_href_anc'][$field_key] != '')) ? "<a href=\"".$req['td_href_anc'][$field_key]."\" ".$td_target_attr." ".$td_title_attr.">".$data[$key][$field_val]."</a>" : $data[$key][$field_val];
				    $td_nowrap = ((isset($req['td_nowrap']) === true) && (isset($req['td_nowrap']) === true)) ? $req['td_nowrap'][$field_key] : '';
				    
					$table .= "<td ".$td_css." ".$td_id_attr." ".$td_name_attr." ".$td_nowrap.">".$td_href_anc."</td>";
				}
				
				if(isset($req['td_actions_button']) === true) {
				    $actions = "";
				    foreach($req['td_actions_button'] as $button) {
				        $file_path = "images/iconarchive/".$button."-16.png";
				        $actions .= "<input type=\"image\" src=\"".$this->view->baseUrl($file_path)."\" id=\"".$button."_action\" title=\"".strtoupper($button)."\" style=\"width:auto; height:auto;vertical-align: middle;\"> ";
				    }
				    $table .= "<td class=\"reportTableLeft\">".$actions."</td>";
				}
				
				
				
				# </TR>
				$table .= '</tr>';
			}
			
			# </TABLE>
			$table .='</table>';
		}
		
		
		return $table;
	}
	
	
	
	
}