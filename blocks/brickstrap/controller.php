<?php  defined('C5_EXECUTE') or die("Access Denied.");

class BrickstrapBlockController extends BlockController {
	
	protected $btTable = 'btBrickstrap';
	protected $btInterfaceWidth = "360";
	protected $btInterfaceHeight = "480";
	protected $btWrapperClass = 'ccm-ui';
	// protected $btCacheBlockRecord = true;
	// protected $btCacheBlockOutput = true;
	// protected $btCacheBlockOutputOnPost = true;
	// protected $btCacheBlockOutputForRegisteredUsers = true;
	// protected $btCacheBlockOutputLifetime = CACHE_LIFETIME;
		
	public function getBlockTypeName() {
		return t("Brickstrap");
	}

	public function getBlockTypeDescription() {
		return t('Grid of responsive bricks lays out links to, and images and text from, single pages.');
	}

	public function solidify($text) {
		$regex = "/\n|\r/i";
		return preg_replace($regex, ', ', Loader::helper('text')->sanitize($text));
	}

	public function getJavaScriptStrings() {
		return array('brick-required' => t('Grid requires brick.'));
	}

	public function generateRandomString(){
		$length = 10;
		$characters = '012345789ABCDEFGHIJKLMNOPRSTUVWXYZ012345789ABCDEFGHIJKLMNOPRSTUVWXYZ012345789ABCDEFGHIJKLMNOPRSTUVWXYZ012345789';
		$string = null;
		for ($p = 0; $p < $length; $p++){$string .= $characters[mt_rand(0,strlen($characters))];}
		return $string;
	}

	function add() {
		$this->set('bricks', array());
		$random = $this->generateRandomString();
		$this->set('random',$random);
	}
	
	function edit() {
		$bricks = $this->getBricks();
		$this->set('bricks', $bricks);
	}

	function view() {

		// $html = Loader::helper('html');
		// $this->addHeaderItem($html->javascript('brickstrap.js', 'jockstrap'));

		$nh = Loader::helper('navigation');
		$current = Page::getCurrentPage();
		$currentID = $current->getCollectionID();
		$path = $current->getCollectionPath();
		$rows = $this->getBricks();
		$bricks = array();
		foreach ($rows as $row) {
			$target = Page::getByID($row['target']);
			$page = $target->getCollectionPath();
			$brick = new stdClass;
			$brick->url = $nh->getLinkToCollection($target);
			$brick->position = $row['position'];
			$brick->title = $row['title'];
			$brick->description = $row['description'];
			$brick->image = $row['image'];
			$brick->cID = $row['target'];
			$brick->cPath = $page;
			$brick->isCurrent = ($currentID === $row['target']);
			$brick->inPath = $this->isPageInPath($path,$page);
			$brick->target = $target;
			$bricks[] = $brick;
		}
		$this->set('bricks', $bricks);
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
	
	function getBricks() {
		$db = Loader::db();
		$sql = 'SELECT * FROM btBricks WHERE bID=' . intval($this->bID) . ' ORDER BY position';
		return $db->getAll($sql);
	}	
	
	function delete(){
		$db = Loader::db();
		$db->query("DELETE FROM btBricks WHERE bID=".intval($this->bID));		
		parent::delete();
	}

	function duplicate($nbID) {
		parent::duplicate($nbID);
		$bricks = $this->getBricks();
		$db = Loader::db();
		$sql = "INSERT INTO btBricks (bID, target, title, description, image, position)"
		 	 . " SELECT ?, target, title, description, image, position FROM btBricks WHERE bID = ?";
		$vals = array($nbID, $this->bID);
		$db->Execute($sql, $vals);
	}
	
	function save($data) {
		$db = Loader::db();
		if(count($data['targets'])){
			//delete existing bricks
			$db->query("DELETE FROM btBricks WHERE bID = ?", array($this->bID));
			
			//loop through and add the bricks
			// $pos = 1;
			$pos = 0;
			foreach($data['targets'] as $target){
				if(intval($target) == 0 || $data['titles'][$pos] == 'tempTitle') continue;
				$sql = "INSERT INTO btBricks (bID, target, title, image, description, position) values (?,?,?,?,?,?)";
				$vals = array($this->bID, $target, $data['titles'][$pos], $data['images'][$pos], $data['descriptions'][$pos], $pos);
				$db->Execute($sql, $vals);
				$pos++;
			}
		}
		parent::save($data);
	}

}

?>
