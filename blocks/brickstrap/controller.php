<?php  defined('C5_EXECUTE') or die("Access Denied.");

class MasonBlockController extends BlockController {
	
	protected $btTable = 'btMason';
	protected $btInterfaceWidth = "360";
	protected $btInterfaceHeight = "480";
	protected $btWrapperClass = 'ccm-ui';
	// protected $btCacheBlockRecord = true;
	// protected $btCacheBlockOutput = true;
	// protected $btCacheBlockOutputOnPost = true;
	// protected $btCacheBlockOutputForRegisteredUsers = true;
	// protected $btCacheBlockOutputLifetime = CACHE_LIFETIME;
		
	public function getBlockTypeName() {
		return t("Mason");
	}

	public function getBlockTypeDescription() {
		return t('Custom navigation lays out pages with images and captions, thanks to Masonry.');
	}

	public function solidify($text) {
		$regex = "/\n|\r/i";
		return preg_replace($regex, ', ', Loader::helper('text')->sanitize($text));
	}

	public function getJavaScriptStrings() {
		return array('one-item-required' => t('You must choose at least one item.'));
	}

	public function generateRandomString(){
		$length = 10;
		$characters = '012345789ABCDEFGHIJKLMNOPRSTUVWXYZ012345789ABCDEFGHIJKLMNOPRSTUVWXYZ012345789ABCDEFGHIJKLMNOPRSTUVWXYZ012345789';
		$string = null;
		for ($p = 0; $p < $length; $p++){$string .= $characters[mt_rand(0,strlen($characters))];}
		return $string;
	}

	function add() {
		$this->setVariables();
		$this->set('items', array());
		$random = $this->generateRandomString();
		$this->set('random',$random);
	}
	
	function edit() {
		$this->setVariables();
		$items = $this->getItems();
		$this->set('items', $items);
		// $this->set('blockIdentifier',$this->getBlockObject()->getProxyBlock() ? $this->getBlockObject()->getProxyBlock()->getInstance()->getIdentifier() : $this->getIdentifier());
	}

	function view() {
		$this->setVariables();
		$nh = Loader::helper('navigation');
		$currentPage = Page::getCurrentPage();
		$currentCID = $currentPage->getCollectionID();
		$currentCPath = $currentPage->getCollectionPath();
		$itemRows = $this->getItems();
		$itemObjs = array();
		foreach ($itemRows as $row) {
			// $item->targetID = $row['targetID'];
			$page = Page::getByID($row['targetID']);
			$itemCPath = $page->getCollectionPath();
			$item = new stdClass;
			$item->url = $nh->getLinkToCollection($page);
			$item->position = $row['position'];
			$item->headline = $row['headline'];
			$item->subhead = $row['subhead'];
			$item->imageID = $row['imageID'];
			$item->cID = $row['targetID'];
			$item->cPath = $itemCPath;
			$item->isCurrent = ($currentCID === $row['targetID']);
			$item->inPath = $this->isPageInPath($currentCPath, $itemCPath);
			$item->cObj = $page;
			$itemObjs[] = $item;
		}
		$this->set('items', $itemObjs);
	}
	//Internal utility function -- tells you if the first path is "under" the second path
	// (meaning that the first path is equal to or begins with the second path).
	//EXCEPT: if underSectionPath is the home page and checkPagePath is *not* the home page,
	// then we always return false (because *every* page in a C5 site is under the home page,
	// so it's not meaningful information)
	private function isPageInPath($checkPagePath, $underSectionPath) {
		//DEV NOTE: All this checking for home pages has a secondary benefit:
		// some users have reported that php warnings are generated when you pass
		// an empty string to the strpos() function, so by checking for home page
		// first, we can avoid calling strpos if either path is empty.
		$pagePathIsHome = empty($checkPagePath);
		$sectionPathIsHome = empty($underSectionPath);
		if ($pagePathIsHome && $sectionPathIsHome) {
			return true;
		} else if ($pagePathIsHome || $sectionPathIsHome) {
			return false;
		} else {
			return (strpos($checkPagePath, $underSectionPath) === 0);
		}
	}
	
	function getItems() {
		$db = Loader::db();
		$sql = 'SELECT * FROM btMasonBrick WHERE bID=' . intval($this->bID) . ' ORDER BY position';
		return $db->getAll($sql);
	}	
	
	function delete(){
		$db = Loader::db();
		$db->query("DELETE FROM btMasonBrick WHERE bID=".intval($this->bID));		
		parent::delete();
	}

	function duplicate($nbID) {
		parent::duplicate($nbID);
		$items = $this->getItems();
		$db = Loader::db();
		$sql = "INSERT INTO btMasonBrick (bID, targetID, headline, subhead, imageID, position)"
		 	 . " SELECT ?, targetID, headline, subhead, imageID, position FROM btMasonBrick WHERE bID = ?";
		$vals = array($nbID, $this->bID);
		$db->Execute($sql, $vals);
	}
	
	function save($data) {
		$db = Loader::db();
		if(count($data['targetIDs']) ){
			//delete existing items
			$db->query("DELETE FROM btMasonBrick WHERE bID = ?", array($this->bID));
			
			//loop through and add the items
			$pos = 0;
			foreach($data['targetIDs'] as $targetID){
				// if(intval($targetID)==0 || $data['headlines'][$pos]=='tempHeadline' || $data['subheads'][$pos]=='tempSubhead' || $data['imageIDs'][$pos]=='tempImageID') continue;
				if(intval($targetID)==0 || $data['headlines'][$pos]=='tempHeadline') continue;
				$sql = "INSERT INTO btMasonBrick (bID, targetID, headline, imageID, subhead, position) values (?,?,?,?,?,?)";
				$vals = array($this->bID, $targetID, $data['headlines'][$pos], $data['imageIDs'][$pos], $data['subheads'][$pos], $pos);
				$db->Execute($sql, $vals);
				$pos++;
			}
		}
		parent::save($data);
	}

	private function setVariables(){
		$th = Loader::helper('concrete/urls');
		$bt = BlockType::getByHandle($this->btHandle);
		$this->set ('action_ajax_fill_data', $th->getBlockTypeToolsURL($bt).'/action_ajax_fill_data');
	}

}

?>