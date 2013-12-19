<?php  defined('C5_EXECUTE') or die("Access Denied."); $th = Loader::helper('text'); ?>

	<div id="ccm-edit-row-<?php  echo $rowInfo['rowID']; ?>" class="ccm-edit-row"><div class="well well-small" style="position: relative">

		<input type="hidden" name="targets[]" value="<?php  echo $rowInfo['target']; ?>">

		<div>
			<strong style="margin-right: 70px"><a title="Open page in new window" href="<?php 
				print BASE_URL.'/index.php?cID='.$rowInfo['target']; ?>" target="_blank"><?php  echo $th->shorten($rowInfo['name'], $numChars = 27, $tail = '...');
			?></a></strong>
			<span style="display: block; width: 60px; height: 20px; position: absolute; top: 9px; right: 9px; text-align: right">
				<a title="Move brick up" onclick="blockForm.moveUp(<?php  echo $rowInfo['rowID']; ?>)" href="javascript:void(0)"><i class="icon-arrow-up"></i></a> 
				<a title="Move brick down" onclick="blockForm.moveDown(<?php  echo $rowInfo['rowID']; ?>)" href="javascript:void(0)"><i class="icon-arrow-down"></i></a> 
				<a title="Remove brick" onclick="blockForm.removeRow(<?php  echo $rowInfo['rowID']; ?>)" href="javascript:void(0)"><i class="icon-remove"></i></a>
			</span>
		</div><br />

		<div>
			<label for="images[]" class="sr-only"><?php  echo t('Thumbnail'); ?></label>
			<div style="background: #fff"><?php 
				$bf = empty($rowInfo['image']) ? null : File::getByID($rowInfo['image']);
				echo Loader::helper('concrete/asset_library')->image('ccm-b-image-'.$rowInfo['rowID'], 'images[]', t('Select art'), $bf);
			?></div>

		</div><br />

		<div>
			<label for="titles[]" class="sr-only"><?php  echo t('Title'); ?></label>
			<?php  
				echo $form->text('titles[]', $rowInfo['title'], array(
					'class' => 'input-block-level',
					'placeholder' => 'Title has as many as 55 characters...',
					'maxlength' => '55'
				));
			?>

		</div><br />

		<div>
			<label for="descriptions[]" class="sr-only"><?php  echo t('Description'); ?></label>
			<?php  
				echo $form->textarea('descriptions[]', $rowInfo['description'], array(
					'class' => 'input-block-level',
					'rows' => '3',
					'placeholder' => 'Short description holds no more than 125 characters...',
					'maxlength' => '125'
				));
			?>

		</div>

	</div></div>
