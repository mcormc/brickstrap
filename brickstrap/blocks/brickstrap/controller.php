<?php namespace Concrete\Package\Brickstrap\Block\Brickstrap;

use Loader;
use CollectionAttributeKey;
use \Concrete\Core\Block\BlockController;
use Page;
use Core;
class Controller extends BlockController {

    protected $btTable = 'btBrickstrap';
    protected $btInterfaceWidth = "700";
    protected $btWrapperClass = 'ccm-ui';
    protected $btInterfaceHeight = "465";

    public function getBlockTypeDescription() {
        return t("Block powers magical grid.");
    }

    public function getBlockTypeName() {
        return t("Brickstrap");
    }

    public function add() {
        $this->requireAsset('redactor');
        $this->requireAsset('core/file-manager');
        $this->requireAsset('core/sitemap');
    }

    public function edit() {
        $this->requireAsset('redactor'); 
        $this->requireAsset('core/file-manager'); 
        $this->requireAsset('core/sitemap');
        $db = Loader::db();
        $items = $db->GetAll('SELECT * from btBrickstrapItem WHERE bID = ? ORDER BY sort', array($this->bID));
        $this->set('items', $items);
    }

    public function view() {
        $db = Loader::db();
        $items = $db->GetAll('SELECT * from btBrickstrapItem WHERE bID = ? ORDER BY sort', array($this->bID));
        $this->set('items', $items);
    }

    public function registerViewAssets() {
        $this->requireAsset('javascript', 'jquery');
        $this->requireAsset('javascript', 'brickstrap');
    }

    public function duplicate($newBID) {
        parent::duplicate($newBID);
        $db = Loader::db();
        $v = array($this->bID);
        $q = 'select * from btBrickstrapItem where bID = ?';
        $r = $db->query($q, $v);
        while ($row = $r->FetchRow()) {
            if(empty($args['cID'][$i])){$args['cID'][$i]=0;}
            if(empty($args['fID'][$i])){$args['fID'][$i]=0;}
            $vals = array($newBID, $row['fID'][$i], $row['cID'][$i], $row['headline'][$i], $row['content'][$i], $row['button'][$i], $row['sort'][$i]);  
            $db->execute('INSERT INTO btBrickstrapItem (bID, fID, cID, headline, content, button, sort) values(?, ?, ?, ?, ?, ?, ?)', $vals);
        }
    }

    public function delete() {
        $db = Loader::db();
        $db->delete('btBrickstrapItem', array('bID' => $this->bID));
        parent::delete();
    }

    public function save($args) {
        $db = Loader::db();
        $db->execute('DELETE from btBrickstrapItem WHERE bID = ?', array($this->bID));
        $count = count($args['sort']);
        $i = 0;
        parent::save($args);
        while ($i < $count) {
            if(empty($args['cID'][$i])){$args['cID'][$i]=0;}
            if(empty($args['fID'][$i])){$args['fID'][$i]=0;}
            $vals = array($this->bID, $args['fID'][$i], $args['cID'][$i], $args['headline'][$i], $args['content'][$i], $args['button'][$i], $args['sort'][$i]);     
            $db->execute('INSERT INTO btBrickstrapItem (bID, fID, cID, headline, content, button, sort) values(?, ?, ?, ?, ?, ?, ?)', $vals);
            $i++;
        }
    }

    public function validate($args) {
        $e = Loader::helper('validation/error');
        if(strlen($args['title'])>255) $e->add(t("Sorry, but the characters in the carousel title cannot exceed 255 characters"));
        $count = count($args['sort']);
        for($i=0;$i<$count;$i++){
            if(strlen($args['headline'][$i])>255) $e->add(t('The headline in item %s is too long. Reduce it to 255 characters or less', $i+1));
            if(strlen($args['button'][$i])>255) $e->add(t('The button text in item %s is too long. Reduce it to 255 characters or less', $i+1));
        }
        return $e;
    }

}