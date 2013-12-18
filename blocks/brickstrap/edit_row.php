<?php  defined('C5_EXECUTE') or die("Access Denied."); $th = Loader::helper('text'); ?>

	<div id="ccm-edit-row-<?php  echo $rowInfo['rowID']; ?>" class="ccm-edit-row"><div class="well well-small" style="position: relative">

		<input type="hidden" name="targetIDs[]" value="<?php  echo $rowInfo['targetID']; ?>">

		<div>
			<strong style="margin-right: 70px"><a title="Open page in new window" href="<?php 
				print BASE_URL.'/index.php?cID='.$rowInfo['targetID']; ?>" target="_blank"><?php  echo $th->shorten($rowInfo['title'], $numChars = 27, $tail = '...');
			?></a></strong>
			<span style="display: block; width: 60px; height: 20px; position: absolute; top: 9px; right: 9px; text-align: right">
				<a title="Move item up" onclick="GridBlockForm.moveUp(<?php  echo $rowInfo['rowID']; ?>)" href="javascript:void(0)"><i class="icon-arrow-up"></i></a> 
				<a title="Move item down" onclick="GridBlockForm.moveDown(<?php  echo $rowInfo['rowID']; ?>)" href="javascript:void(0)"><i class="icon-arrow-down"></i></a> 
				<a title="Remove item" onclick="GridBlockForm.removeRow(<?php  echo $rowInfo['rowID']; ?>)" href="javascript:void(0)"><i class="icon-remove"></i></a>
			</span>
		</div><br />

		<div>
			<label for="imageIDs[]" class="sr-only"><?php  echo t('Thumbnail'); ?></label>
			<div style="background: #fff"><?php 
				$bf = empty($rowInfo['imageID']) ? null : File::getByID($rowInfo['imageID']);
				echo Loader::helper('concrete/asset_library')->image('ccm-b-image-'.$rowInfo['rowID'], 'imageIDs[]', t('Select art'), $bf);
			?></div>

		</div><br />

		<div>
			<label for="headlines[]" class="sr-only"><?php  echo t('Headline'); ?></label>
			<?php  
				echo $form->text('headlines[]', $rowInfo['headline'], array(
					'class' => 'input-block-level',
					'placeholder' => 'Headline has as many as 55 characters...',
					'maxlength' => '55'
				));
			?>

		</div><br />

		<div>
			<label for="subheads[]" class="sr-only"><?php  echo t('Description'); ?></label>
			<?php  
				echo $form->textarea('subheads[]', $rowInfo['subhead'], array(
					'class' => 'input-block-level',
					'rows' => '3',
					'placeholder' => 'Short description holds no more than 125 characters...',
					'maxlength' => '125'
				));
			?>

		</div>

	</div></div>