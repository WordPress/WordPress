/*
 
 $Id: sitemap.js 48032 2008-05-27 14:32:06Z arnee $

*/

function sm_addPage(url,priority,changeFreq,lastChanged) {

	var table = document.getElementById('sm_pageTable').getElementsByTagName('TBODY')[0];
	var ce = function(ele) { return document.createElement(ele) };
	var tr = ce('TR');
												
	var td = ce('TD');
	var iUrl = ce('INPUT');
	iUrl.type="text";
	iUrl.style.width='95%';
	iUrl.name="sm_pages_ur[]";
	if(url) iUrl.value=url;
	td.appendChild(iUrl);
	tr.appendChild(td);
	
	td = ce('TD');
	td.style.width='150px';
	var iPrio = ce('SELECT');
	iPrio.style.width='95%';
	iPrio.name="sm_pages_pr[]";
	for(var i=0; i <priorities.length; i++) {
		var op = ce('OPTION');
		op.text = priorities[i];		
		op.value = priorities[i];
		try {
			iPrio.add(op, null); // standards compliant; doesn't work in IE
		} catch(ex) {
			iPrio.add(op); // IE only
		}
		if(priority && priority == op.value) {
			iPrio.selectedIndex = i;
		}
	}
	td.appendChild(iPrio);
	tr.appendChild(td);
	
	td = ce('TD');
	td.style.width='150px';
	var iFreq = ce('SELECT');
	iFreq.name="sm_pages_cf[]";
	iFreq.style.width='95%';
	for(var i=0; i<changeFreqVals.length; i++) {
		var op = ce('OPTION');
		op.text = changeFreqNames[i];		
		op.value = changeFreqVals[i];
		try {
			iFreq.add(op, null); // standards compliant; doesn't work in IE
		} catch(ex) {
			iFreq.add(op); // IE only
		}
		
		if(changeFreq && changeFreq == op.value) {
			iFreq.selectedIndex = i;
		}
	}
	td.appendChild(iFreq);
	tr.appendChild(td);
	
	var td = ce('TD');
	td.style.width='150px';
	var iChanged = ce('INPUT');
	iChanged.type="text";
	iChanged.name="sm_pages_lm[]";
	iChanged.style.width='95%';
	if(lastChanged) iChanged.value=lastChanged;
	td.appendChild(iChanged);
	tr.appendChild(td);
	
	var td = ce('TD');
	td.style.textAlign="center";
	td.style.width='5px';
	var iAction = ce('A');
	iAction.innerHTML = 'X';
	iAction.href="javascript:void(0);"
	iAction.onclick = function() { table.removeChild(tr); };
	td.appendChild(iAction);
	tr.appendChild(td);
	
	var mark = ce('INPUT');
	mark.type="hidden";
	mark.name="sm_pages_mark[]";
	mark.value="true";
	tr.appendChild(mark);
	
	
	var firstRow = table.getElementsByTagName('TR')[1];
	if(firstRow) {
		var firstCol = (firstRow.childNodes[1]?firstRow.childNodes[1]:firstRow.childNodes[0]);
		if(firstCol.colSpan>1) {
			firstRow.parentNode.removeChild(firstRow);
		}
	}
	var cnt = table.getElementsByTagName('TR').length;
	if(cnt%2) tr.className="alternate";
	
	table.appendChild(tr);										
}

function sm_loadPages() {
	for(var i=0; i<pages.length; i++) {
		sm_addPage(pages[i].url,pages[i].priority,pages[i].changeFreq,pages[i].lastChanged);
	}
}