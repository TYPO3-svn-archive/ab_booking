<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009-2011 Alexander Bigga <linux@bigga.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * ab_booking misc functions
 *
 * @author	Alexander Bigga <linux@bigga.de>
 * @package	TYPO3
 * @subpackage	tx_abbooking
 */
class tx_abbooking_form {

	/**
	 * Request Formular
	 * The customer enters his personal data and submits the form.
	 *
	 * @param	[type]		$conf: ...
	 * @param	integer		$stage of booking process
	 * @param	[type]		$stage: ...
	 * @return	HTML		form with booking details
	 */
	public function printUserFormElements($showErrors = 0, $showHidden = 0) {
		
		$product = $this->lConf['productDetails'][$this->lConf['AvailableProductIDs'][0]];
		$customer = $this->lConf['customerData'];
		
//~ if ($showErrors > 0)
//~ 	print_r($this->lConf['form']);
	
		foreach ($this->lConf['form'] as $formname => $form) {
			
			$formname = str_replace('.', '', $formname);
			if ($form['required'] == 1)
				$cssClass = 'item ' . $formname . ' required';
			else
				$cssClass = 'item ' . $formname;
			$formnameGET = $this->prefixId.'['.$formname.']';
			
			unset($cssError);
			switch ($form['type']) {
				case 'input':
					
					if (!empty($form['error']))
						$cssClass .= ' errorField';
					$out .= '<div class="'.$cssClass.'">'.$this->getTSTitle($form['title.']).'<br />';
					if (!empty($form['error'])) {
						$cssError = 'class="error"';
						$out .= '<p class="errorText">'.$form['error'].'</p>';
					}
					if ($showHidden == 1) {
						$type = 'hidden';
						$out .= '<p class="yourSettings">'.$customer[$formname].'</p>';
					}
					else
						$type = 'text';

					if ($formname == 'checkinDate' && $showHidden == 0)
						$out .= tx_abbooking_div::getJSCalendarInput($formnameGET, $this->lConf['startDateStamp'], $form['error']);
					else
						$out .= '<input '.$cssError.' name='.$formnameGET.' type="'.$type.'" size="'.$form['size'].'" maxlength="'.(empty($form['maxsize']) ? $form['size'] : $form['maxsize'] ).'" value="'.$customer[$formname].'"/>';
						
					$out .= '</div>';
					break;
				case 'radio':
					$out .= '<div class="'.$cssClass.'">'.$this->getTSTitle($form['title.']).'<br />';
					foreach ($form['radio.'] as $radioname => $radio) {
						if ($radio['selected'] == 1)
							$selected = 'checked="checked"';
						else
							$selected = '';
						$out .= '<div class="singleradio"><input type="radio" name="'.$formnameGET.'" value="'.$radioname.'" '.$selected.' />'.$this->getTSTitle($radio['title.']).'</div>';
					}
					$out .= '<div class="clearsingleradio"></div>';
					$out .= '</div>';
				
					break;
				case 'checkbox':
					break;
				case 'selector':
				
					$selected='selected="selected"';
					$out .= '<div class="'.$cssClass.'">'.$this->getTSTitle($form['title.']).'<br />';
					$out .= '<select name="'.$formnameGET.'" size="1">';
					switch($formname) {
						case 'adultSelector':
							if (isset($this->lConf['numPersons']))
								if ($this->lConf['numPersons'] > $product['capacitymax'])
									$selNumPersons[$product['capacitymax']] = $selected;
								else if ($this->lConf['numPersons'] < $product['capacitymin'])
									$selNumPersons[$product['capacitymin']] = $selected;
								else
									$selNumPersons[$this->lConf['numPersons']] = $selected;
							else
								$selNumPersons[2] = $selected;
								
							/* how many persons are possible? */
							for ($i = $product['capacitymin']; $i<=$product['capacitymax']; $i++) {
								$out.='<option '.$selNumPersons[$i].' value='.$i.'>'.$i.' </option>';
							}
								
						break;
						case 'childSelector':
						break;
						case 'teenSelector':
						break;
						case 'daySelector':
							if (isset($this->lConf['numNights']))
								$selNumNights[$this->lConf['numNights']] = $selected;
							else
								$selNumNights[2] = $selected;
								
							for ($i = $product['minimumStay']; $i <= $product['maxAvailable']; $i+=$product['daySteps']) {
									$endDate = strtotime('+'.$i.' day', $this->lConf['startDateStamp']);
									$out.='<option '.$selNumNights[$i].' value='.$i.'>'.$i.' ('.strftime('%d.%m.%Y', $endDate).')</option>';
							}
								
						break;
					}

					$out .= '</select>';
					$out .= '</div>';

					break;
				case 'textarea':
					$out .= '<div class="'.$cssClass.'">'.$this->getTSTitle($form['title.']).'<br />
					<textarea name='.$formnameGET.' cols="50" rows="'.(int)($form['size']/50).'" wrap="PHYSICAL">'.$customer[$formname].'
					</textarea>
					</div>';
					break;
				case 'infobox':
					$out .= '<div class="'.$cssClass.'">'.$this->getTSTitle($form['title.']).'<br />';
					$out .= '<p>'.$this->getTSTitle($form['info.']).'</p>';
					$out .= '</div>';
					break;
				default:
					break;
			}
//~ 			$out .= $formname.": ".$form['type'].": ".$form['required'].": ".$form['size']."\n";
			
		}
		return $out;
	}

	/**
	 * Request Formular
	 * The customer enters his personal data and submits the form.
	 *
	 * @param	[type]		$conf: ...
	 * @param	integer		$stage of booking process
	 * @param	[type]		$stage: ...
	 * @return	HTML		form with booking details
	 */
	public function printUserForm($stage) {

		
		$interval = array();
		$product = $this->lConf['productDetails'][$this->lConf['AvailableProductIDs'][0]];
//~ print_r($product);		
		$customer = $this->lConf['customerData'];
		
		// first check errors...
		if (empty($product)) {
			$content = '<h2 class="setupErrors"><b>'.$this->pi_getLL('error_noProductSelected').'</b></h2>';
			return $content;
		}

		$interval['startDate'] = $this->lConf['startDateStamp'];
		$interval['endDate'] = $this->lConf['endDateStamp'];
		$interval['startList'] = strtotime('-2 day', $interval['startDate']);
		$interval['endList'] = strtotime('+2 day', $interval['startDate']);

		if ($stage > 1) {
			$numErrors = tx_abbooking_form::formVerifyUserInput();
			if ($stage == 2 && $numErrors > 0)
					$stage = 1;
			else
					$stage = 3;
			if ($stage == 3 && $numErrors > 0)
					$stage = 2;
		}

		$content .= tx_abbooking_div::printBookingStep($stage);

		$content .='<div class="requestForm">';

		$content .='<h3>'.htmlspecialchars($this->pi_getLL('title_request')).' '.$product['detailsRaw']['header'].'</h3>';

		$content .= '<p class=available><b>'.$this->pi_getLL('result_available').'</b>';
		$content .= ' '.strftime("%A, %d.%m.%Y", $this->lConf['startDateStamp']).' - ';
		$availableMaxDate = strtotime('+ '.$product['maxAvailable'].' days', $this->lConf['startDateStamp']);
		$content .= ' '.strftime("%A, %d.%m.%Y", $availableMaxDate);
		$content .= '</p><br />';
		
		// show calendars following TS settings
		if ($this->lConf['form']['showCalendarMonth']>0) {
			$intval['startDate'] = strtotime('first day of this month', $interval['startDate']);
			$intval['endDate'] = strtotime('+'.$this->lConf['form']['showCalendarMonth'].' months', $intval['startDate'])-86400;
			$content .= tx_abbooking_div::printAvailabilityCalendarDiv($this->lConf['ProductID'], $intval);
		} else if ($this->lConf['form']['showCalendarWeek']>0) {
			$intval['startDate'] = $interval['startDate'];
			$intval['endDate'] = strtotime('+'.$this->lConf['form']['showCalendarWeek'].' weeks', $interval['startDate']);
			$content .= tx_abbooking_div::printAvailabilityCalendarLine($this->lConf['ProductID'], $intval);
		} else
			$content .= tx_abbooking_div::printAvailabilityCalendarLine($this->lConf['ProductID'], $interval);



		$selected='selected="selected"';
		if (isset($this->lConf['numPersons']))
			if ($this->lConf['numPersons'] > $product['capacitymax'])
				$selNumPersons[$product['capacitymax']] = $selected;
			else if ($this->lConf['numPersons'] < $product['capacitymin'])
				$selNumPersons[$product['capacitymin']] = $selected;
			else
				$selNumPersons[$this->lConf['numPersons']] = $selected;
		else
			$selNumPersons[2] = $selected;

		if (isset($this->lConf['numNights']))
			$selNumNights[$this->lConf['numNights']] = $selected;
		else
			$selNumNights[2] = $selected;

		$contentError = '';
		/* handle errors */
		if (isset($this->form_errors['vacancies'])) {
			$ErrorVacancies='class="error"';
			$contentError.='<li>'.$this->form_errors['vacancies'].'</li>';
		}
		if (isset($this->form_errors['vacancies_limited'])) {
			$ErrorVacanciesLimited='class="error"';
			$contentError.='<li>'.$this->form_errors['vacancies_limited'].'</li>';
		}
		if (isset($this->form_errors['startDateInThePast'])) {
			$ErrorVacancies='class="error"';
			$contentError.='<li>'.$this->form_errors['startDateInThePast'].'</li>';
		}
		if (isset($this->form_errors['endDateNotValid'])) {
			$ErrorVacanciesLimited='class="error"';
			$contentError.='<li>'.$this->form_errors['endDateNotValid'].'</li>';
		}
		if (isset($this->form_errors['numNightsNotValid'])) {
			$ErrorVacanciesLimited='class="error"';
			$contentError.='<li>'.$this->form_errors['numNightsNotValid'].'</li>';
		}

		if ($product['minimumStay'] > $product['maxAvailable']) {
			$ErrorVacanciesLimited='class="error"';
			if ($product['minimumStay'] == 1)
				$text_periods = ' '.$this->pi_getLL('period');
			else
				$text_periods = ' '.$this->pi_getLL('periods');

			$contentError.='<li>'.$this->pi_getLL('error_minimumStay').' '.$product['minimumStay'].' '.$text_periods.'</li>';
		}

		if (!empty($contentError)) {
			$content.='<div class="errorForm">';
			$content.='<ul>';
			$content.= $contentError;
			$content.='</ul>';
			$content.='</div>';
		}

		// check if configured email is present
		if (version_compare(TYPO3_version, '4.5', '<'))
			if ((!class_exists('tx_abswiftmailer_pi1') || !$this->lConf['useSwiftMailer']) && empty($this->lConf['EmailAddress'])) {
				$content.= '<h2 class="setupErrors"><b>'.$this->pi_getLL('error_noEmailConfigured').'</b></h2>';
			}








		/* handle stages */
		if ($stage == 3) {
			$content.='<div class="noteForm"><p>'.htmlspecialchars($this->pi_getLL('please_confirm')).'</p></div>';

			$SubmitButtonEdit=htmlspecialchars($this->pi_getLL('submit_button_edit'));
			$SubmitButton=htmlspecialchars($this->pi_getLL('submit_button_final'));

			$content .= '<form  class="requestForm" action="'.$this->pi_getPageLink($this->lConf['gotoPID']).'" method="POST">';
			$content .= tx_abbooking_form::printUserFormElements(0, $showHidden = 1);
			$content .= $this->printCalculatedRates($product['uid'], $this->lConf['numNights'], 1);

			$params_united = $this->lConf['startDateStamp'].'_'.$this->lConf['numNights'].'_'.$this->lConf['numPersons'].'_'.$this->lConf['ProductID'].'_'.$this->lConf['uidpid'].'_'.$this->lConf['PIDbooking'].'_bor'.($stage);
			$params = array (
				$this->prefixId.'[ABx]' => $params_united,
			);


			$content .= '<input type="hidden" name="'.$this->prefixId.'[ABx]" value="'.$params_united.'">';
			$content .= '<input type="hidden" name="'.$this->prefixId.'[ABwhatToDisplay]" value="BOOKING">
							<div class="buttons">
							<input class="edit" type="submit" name="'.$this->prefixId.'[submit_button_edit]" value="'.$SubmitButtonEdit.'">
							<input class="submit_final" type="submit" name="'.$this->prefixId.'[submit_button]" value="'.$SubmitButton.'">
							</div>
				</form>';

		}
		else {
			$SubmitButton=htmlspecialchars($this->pi_getLL('submit_button_check'));

			if (isset($this->lConf['startDateStamp']))
				$startdate = $this->lConf['startDateStamp'];
			else
				$startdate = time();

			$content .= '<form  class="requestForm" action="'.$this->pi_getPageLink($this->lConf['gotoPID']).'" method="POST">';
			$content .= tx_abbooking_form::printUserFormElements($numErrors, 0);
			$content .= $this->printCalculatedRates($product['uid'], $this->lConf['numNights'], 1);

			$params_united = '0_0_0_'.$this->lConf['ProductID'].'_'.$this->lConf['uidpid'].'_'.$this->lConf['PIDbooking'].'_bor'.($stage + 1);
			$params = array (
				$this->prefixId.'[ABx]' => $params_united,
			);

			$content .= '<input type="hidden" name="'.$this->prefixId.'[ABx]" value="'.$params_united.'">';
			$content .=	'<input type="hidden" name="'.$this->prefixId.'[ABwhatToDisplay]" value="BOOKING"><br/>
							<input class="submit" type="submit" name="'.$this->prefixId.'[submit_button]" value="'.$SubmitButton.'">
				</form>';

		}
		$content.='</div>';
		return $content;
	}
	
	/**
	 * Return an input field with date2cal-calendar if available
	 *
	 * @param	string		$name: of the input field
	 * @param	string		$value: of the input field
	 * @param	boolean		$error: if set the css class "error" is added
	 * @return	HTML-input		field for date selection
	 */
	function getJSCalendarInput($name, $value, $error = '') {

		if (class_exists('JSCalendar')) {
			if ($this->conf['dateFormat'] != '') {
				$dateFormat = str_replace(array('d', 'm', 'y', 'Y'), array('%d', '%m', '%y', '%Y'), $this->conf['dateFormat']);
			} else {
				// unfortunately, the jscalendar doesn't recognize %x as dateformat
				if ($GLOBALS['TSFE']->config['config']['language'] == 'de')
					$dateFormat = '%d.%m.%Y';
				else if ($GLOBALS['TSFE']->config['config']['language'] == 'en')
					$dateFormat = '%d/%m/%Y';
				else
					$dateFormat = '%Y-%m-%d';
			}
			$JSCalendar = JSCalendar::getInstance();
			// datetime format (default: time)
            $JSCalendar->setDateFormat(false, $dateFormat);
			$JSCalendar->setNLP(false);
            $JSCalendar->setInputField($name);
			$JSCalendar->setConfigOption('ifFormat', $dateFormat);
 			$out .= $JSCalendar->render(strftime($dateFormat, $value));

			if (($jsCode = $JSCalendar->getMainJS()) != '') {
				$GLOBALS['TSFE']->additionalHeaderData['abbooking_jscalendar'] = $jsCode;
			}
		} else {
			$out .= '<input '.$errorClass.' type="text" class="jscalendar" name="'.$name.'" id="'.$name.'" value="'.strftime('%x', $value).'" ><br/>';
		}

		if (isset($error)) {
			$out = str_replace('class="jscalendar"', 'class="jscalendar error"', $out);
		}

		return $out;
	}
	
	/*
	 * Checks the form data for validity
	 *
	 * @return	amount		of errors found
	 */
	function formVerifyUserInput() {

		$this->form_errors = array();
		$numErrors = 0;
		$dns_ok = 0;

		if (empty($this->lConf['productDetails'])) {
			$content = '<h2 class="setupErrors"><b>'.$this->pi_getLL('error_noProductSelected').'</b></h2>';
			return $content;
		} else {
			foreach ( $this->lConf['productDetails'] as $key => $val ) {
				$product = $val;
			}
		}
		$customer = $this->lConf['customerData'];

		foreach ($this->lConf['form'] as $formname => $form) {
			$formname = str_replace('.', '', $formname);

			switch ($formname) {
				case 'email':
					if ($form['required'] == 1) {
						// Email mit Syntax und Domaincheck
						$motif1="#^[[:alnum:]]([[:alnum:]\._-]{0,})[[:alnum:]]";
						$motif1.="@";
						$motif1.="[[:alnum:]]([[:alnum:]\._-]{0,})[\.]{1}([[:alpha:]]{2,})$#";

						if (preg_match($motif1, $customer['email'])){
							list($user, $domain)=preg_split('/@/', $customer['email'], 2);
							$dns_ok=checkdnsrr($domain, "MX");
						}
						if (!$dns_ok || !t3lib_div::validEmail($customer['email'])){
							$this->lConf['form'][$formname.'.']['error'] = is_array($form['errorText.']) ? $this->getTSTitle($form['errorText.']) : $this->pi_getLL('error_email');
							$numErrors++;
						}
					}
					break;
				case 'checkinDate':
					if ($form['required'] == 1) {
						if ($this->lConf['startDateStamp'] < (time()-86400)) {
							$this->lConf['form'][$formname.'.']['error'] = $this->pi_getLL('error_startDateInThePast');
							$numErrors++;
						}
						if (empty($customer[$formname])) {
							$this->lConf['form'][$formname.'.']['error'] = is_array($form['errorText.']) ? $this->getTSTitle($form['errorText.']) : $this->pi_getLL('error_required'); 
							$numErrors++;
						}
					}					
					break;
				case 'daySelector':
					if ($form['required'] == 1) {
						if (empty($customer[$formname]) || $customer[$formname] == 0) {
							$this->lConf['form'][$formname.'.']['error'] = is_array($form['errorText.']) ? $this->getTSTitle($form['errorText.']) : $this->pi_getLL('error_numNightsNotValid'); 
							$numErrors++;
						}
					}
					break;
					
				default:
					if ($form['required'] == 1 && empty($customer[$formname])) {
						$this->lConf['form'][$formname.'.']['error'] = is_array($form['errorText.']) ? $this->getTSTitle($form['errorText.']) : $this->pi_getLL('error_required'); 
						$numErrors++;
					}
					
					break;
			}
		}

		// check for limited vacancies...
		if ($product['maxAvailable'] < $this->lConf['numNights']) {
			$form_errors['vacancies_limited'] = $this->pi_getLL('error_vacancies_limited');
			$numErrors++;
		}

		return $numErrors;
		
//~ 		if ($this->lConf['startDateStamp']+86400 > $this->lConf['endDateStamp']) {
//~ 			$this->form_errors['endDateNotValid'] = $this->pi_getLL('error_endDateNotValid')."<br/>";
//~ 			$numErrors++;
//~ 		}

//~ 		if (empty($this->lConf['numNights'])) {
//~ 			$this->form_errors['numNightsNotValid'] = $this->pi_getLL('error_numNightsNotValid')."<br/>";
//~ 			$numErrors++;
//~ 		}

		return $numErrors;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ab_booking/lib/class.tx_abbooking_form.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ab_booking/lib/class.tx_abbooking_form.php']);
}
?>