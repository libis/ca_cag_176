/* ----------------------------------------------------------------------
 * js/image/image.js
 * ----------------------------------------------------------------------
 */

var viewerWindow;

	function initImageApp(imagedata){
		imagedefaultdata = {
			defaultSearchText : "Search Image ID ... ",
//			defaultImageBaseUrl : "http://aleph08.libis.kuleuven.be:8881/dtl-cgi",
			defaultImageBaseUrl : "http://resolver.libis.be",
			lookupUrl	: "/ca_cag/index.php/lookup/Image/Get"
		};
		imagedata = jQuery.extend(imagedefaultdata, imagedata);
		imageInitializeEvents(imagedata);
		return imagedata;
	}
	
	function initNewImage(imagedata){
	//
	}
	
	function initExistingImage(imagedata){
		imagedata.imageContainer.find('.imagePID:first').val(imagedata.imagePID)
		pids = imagedata.imagePID
		thumbnail_div = jQuery(imagedata.imageContainer).find('#image_thumbnail');
		thumbnail_div.empty()
		if (pids){
            defaultImageUrl = 'http://resolver.libis.be/';
			imageObjectDate = pids.split('_,_');
			imageObjectPid = imageObjectDate[0];
            imageObjectThumb =  defaultImageUrl + imageObjectPid + '/';
            imageObjectView = defaultImageUrl + imageObjectPid + '/representation';
			thumbnail_div.append("<img src='"+ imageObjectThumb +"' view='"+imageObjectView +"'>");
		}
	}


	function imageInitializeEvents(imagedata){
//		console.log(imagedata);
		jQuery(imagedata.imageContainer).find('.imageSearchField:first').blur(function() {
			setDefaultText(this, imagedata.defaultSearchText);
		});
		jQuery(imagedata.imageContainer).find('.imageSearchField:first').click(function() {
			clearDefaultText(this, imagedata.defaultSearchText);
		});
		jQuery(imagedata.imageContainer).find('#searchImageButton').live('click', function(event) {
				event.preventDefault();
				search_thumbnail_div = jQuery(imagedata.imageContainer).find('#image_search_thumbnails');
				jQuery(imagedata.imageContainer).find('div.imageSearchBox').append( "<img src='" + imagedata.processIndicator + "' border='0' id='processIndicator' style='margin-left:1em;'/>");
				
				searchlabel= jQuery(imagedata.imageContainer).find('.imageSearchField:first').val();

				lookupUrl=imagedata.lookupUrl;
				search_thumbnail_div.empty();
				
				jQuery.getJSON(lookupUrl, { q: searchlabel, _context_id:0 }, function(data) { 
				   // search_thumbnail_div.empty();
					jQuery.each(data, function(i,item){
							search_thumbnail_div.append("<div style='float: left;'><img src='"+ item.thumbnail +"' id='"+ item.pid +"'  class='imageImage' style='margin:0.3em; cursor:pointer; max-height: 150px;' view='"+ item.view +"'><br><p class='caption' style='display:inline;'>" + item.label + "</p></div>");
						if (item == data[data.length-1]){
							jQuery(search_thumbnail_div).find('img#'+ item.pid ).load(function () {
								jQuery(imagedata.imageContainer).find('div.imageSearchBox').find('#processIndicator').remove();
								
								search_thumbnail_div.prepend('<h3>Search results for :'+ searchlabel +'</h3>');
								jQuery(imagedata.imageContainer).find('#image_search_thumbnails').show();
							});
						}
					});
					jQuery(imagedata.imageContainer).find('div.imageSearchBox img#processIndicator').remove();
				})

		});
		
		jQuery(imagedata.imageContainer).find('#image_search_thumbnails').find('img.imageImage').live('click', function(event) {
			imagedata.imagePID = jQuery(this).attr("id") +"_,_"+ jQuery(this).attr("src") +"_,_"+ jQuery(this).attr("view");
			initExistingImage(imagedata)
			jQuery(imagedata.imageContainer).find('#image_search_thumbnails').hide();
		});

		jQuery(imagedata.imageContainer).find('#image_thumbnail').find('img').live('click', function(event) {
			if ( typeof viewerWindow != "undefined"){
			    viewerWindow.close()
			}
			viewerWindow = window.open( $(this).attr('view'), 'imageViewer');
			return false;
		});

		jQuery(imagedata.imageContainer).find('#image_search_thumbnails').css({
			'background-color': '#FFF',
			'border': '1px solid #000',
			'font-size': '0.85em',
			'position': 'absolute',
			'z-index': '1',
			'display': 'none',
			'margin-top': '-1em',
			'overflow': 'auto'
		});
		
/*		
		jQuery(mapdata.mapholder).find('.mapSuggestLink').live('click mouseover mouseout', function(event) {
			if (event.type == 'click') {
				jQuery(this).attr("class",".mapSuggestLink selected");
				findSelectedSuggest(mapdata);
			} else if (event.type == 'mouseover') {
				setSelectedSuggest(mapdata,jQuery('#mapSearchSuggest .mapSuggestLink').index(jQuery(this)));
			} else {
				setSelectedSuggest(mapdata,-1); 
			}
		});
*/
	
	}
	
	
	
	function parseXml(xml){
		  jQuery(xml).find("result").each(function() {
		 		console.log ( jQuery(this) );
		  });
	}
	
	
	function setDefaultText(thisI, defaultText) {
		if (thisI.value == '') {
			thisI.value = defaultText;
		}
	}
	
	function clearDefaultText(thisI, defaultText) {

		if (thisI.value == defaultText) {
			thisI.value = '';
		}
	}
	