GridSitemapNode = function(targetID, title) {
	GridBlockForm.addRow(targetID, title);
}



var GridBlockForm = {

	init: function(){},

	addRowNewId: 0,

	addRow: function(targetID, title) {
		this.addRowNewId--; // negative counter - so it doesn't compete with existing rowIDs
		var rowID = this.addRowNewId;
		var templateHTML = $('#rowTemplateWrap .ccm-edit-row').html();
		templateHTML = templateHTML.replace(/tempRowID/g, rowID);
		templateHTML = templateHTML.replace(/tempTargetID/g, targetID);
		templateHTML = templateHTML.replace(/tempTitle/g, title);
		var newRow = document.createElement("div");
		newRow.innerHTML = templateHTML;
		newRow.id = 'ccm-edit-row-' + parseInt(rowID);
		newRow.className = 'ccm-edit-row';
		document.getElementById('ccm-edit-rows').appendChild(newRow);
	},

	removeRow: function(rowID) {
		$('#ccm-edit-row-' + rowID).remove();
	},

	moveUp: function(rowID) {
		var thisRow = $('#ccm-edit-row-' + rowID);
		var qIDs = this.serialize();
		var previousQID = 0;
		for (var i = 0; i < qIDs.length; i++) {
			if (qIDs[i] == rowID) {
				if (previousQID == 0) break; 
				thisRow.after($('#ccm-edit-row-' + previousQID));
				break;
			}
			previousQID = qIDs[i];
		}	 
	},

	moveDown: function(rowID) {
		var thisRow = $('#ccm-edit-row-' + rowID);
		var qIDs = this.serialize();
		var thisQIDfound = 0;
		for(var i = 0; i< qIDs.length; i++) {
			if(qIDs[i] == rowID){
				thisQIDfound = 1;
				continue;
			}
			if(thisQIDfound) {
				$('#ccm-edit-row-' + qIDs[i]).after(thisRow);
				break;
			}
		}
	},
	serialize: function() {
		var t = document.getElementById("ccm-edit-rows");
		var qIDs = [];
		for(var i = 0; i < t.childNodes.length; i++) { 
			if(t.childNodes[i].className && t.childNodes[i].className.indexOf('ccm-edit-row') >= 0){
				var qID = t.childNodes[i].id.replace('ccm-edit-row-','');
				qIDs.push(qID);
			}
		}
		return qIDs;
	},

	validate: function() {
		qIDs = this.serialize();
		if (qIDs.length < 1){
			ccm_addError(ccm_t('one-item-required'));
		}
		return false;
	}
}

ccmValidateBlockForm = function() { return GridBlockForm.validate(); }