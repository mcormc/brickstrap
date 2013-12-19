<?php  defined('C5_EXECUTE') or die("Access Denied."); $ah = Loader::helper('concrete/interface'); ?>

<div style="text-align: right"><a id="ccm-edit-addRow" class="btn btn-inverse" dialog-title="<?php  echo t('Choose Page'); ?>" href="<?php  
	echo REL_DIR_FILES_TOOLS_REQUIRED; ?>/sitemap_search_selector.php?sitemap_select_mode=select_page&callback=brickNode"><?php  echo t('Add brick');
?></a></div><hr />

<div id="ccm-edit-rows">
<?php  foreach($bricks as $rowInfo) {

	$rowInfo['rowID'] = $rowInfo['position'];
	$p = Page::getByID($rowInfo['target']);
	$rowInfo['name'] = $p->getCollectionName();

	$this->inc('row.php', array('rowInfo' => $rowInfo));
} ?>
</div>

<div id="rowTemplateWrap" style="display:none">
<?php 
$tmpRowInfo = array(
	'rowID' => 'tempRowID',
	'target' => 'tempTarget',
	'title' => 'tempTitle',
	'name' => 'tempTitle'
);
$this->inc('row.php', array('rowInfo' => $tmpRowInfo));
?>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#ccm-edit-addRow').unbind();
		$('#ccm-edit-addRow').dialog();
	});
</script>
