<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<tr id="ria_pd_list_form">
	<td colspan="2">
		<table width="100%" border="0" cellpadding="2" cellspacing="2">
<tr >

	<td align="right" width="40%"><?= GetMessage("BPGP_IBLOCK_ID_TITLE") ?>:</td>
	<td width="60%">
	<input type="text" size="50" id="id_requested_iblock_id" name="requested_iblock_id" value="<?=$arCurrentValues['requested_iblock_id'] ?>">
	<input type="button" value="..." onclick="BPAShowSelector('id_requested_iblock_id', 'string');">
		
	</td>

</tr>
<tr >

	<td align="right" width="40%"><?= GetMessage("BPGP_REC_ID_TITLE") ?>:</td>
	<td width="60%">
	<input type="text" size="50" id="id_requested_rec_id" name="requested_rec_id" value="<?=$arCurrentValues['requested_rec_id'] ?>">
	<input type="button" value="..." onclick="BPAShowSelector('id_requested_rec_id', 'string');">
		
	</td>

</tr>
<tr >

	<td align="right" width="40%"><?= GetMessage("BPGP_PROPERTY_TITLE") ?>:</td>
	<td width="60%">
	<input type="text" size="50" id="id_requested_property_name" name="requested_property_name" value="<?=$arCurrentValues['requested_property_name'] ?>">
	<input type="button" value="..." onclick="BPAShowSelector('id_requested_property_name', 'string');">
		
	</td>

</tr>
</table>
