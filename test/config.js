/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.width	= '500';
	config.height	= '200';
	
	config.extraPlugins	= 'bbcode';
	config.extraPlugins = 'syntaxhighlight';
 
	config.toolbar_Editor =
	[
		{ name: 'document', items : [ 'NewPage', 'Save', 'Preview','Print'] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','Scayt' ] },
		{ name: 'styles', items : [ 'Font','FontSize' ] },
		{ name: 'basicstyles', items : [ 'Bold','Italic', 'Underline', 'Strike','-', 'Subscript', 'Superscript', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-'] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent' ] },
		{ name: 'links', items : [ 'Link','Unlink','Image','Smiley','SpecialChar'] },
		{ name: 'tools', items : [ 'Table','HorizontalRule' ] },
		{ name: 'blocks', items : [ 'Blockquote' , 'Source']}		
	];

	config.toolbar_Basic =
	[
		{ name: 'basicstyles', items : [ 'Bold','Italic', 'Underline'] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent' ] },
		{ name: 'links', items : [ 'Link','Unlink'] },
		{ name: 'links', items : [ 'Smiley','SpecialChar'] }
	];
};
