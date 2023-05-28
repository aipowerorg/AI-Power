(function() {
    var $ = jQuery;
    const createLoadingSpinner = function (selectedNode, placement, loadingSpinnerId) {

        let spinnerHtml = '';
        if (['li'].includes(selectedNode.tagName.toLowerCase())) {
            spinnerHtml = '<' + selectedNode.tagName + ' id="' + loadingSpinnerId + '" class="wpaicg-mce-loading">&nbsp;</' + selectedNode.tagName + '>';
        } else {
            spinnerHtml = '<p id="' + loadingSpinnerId + '" class="wpaicg-mce-loading">&nbsp;</p>';
        }

        return spinnerHtml;
    }
    tinymce.PluginManager.add( 'wpaicgeditor', function( editor, url ) {
        let wpaicg_menus = [];
        if(typeof wpaicgTinymceEditorMenus === "object"){
            for(let i =0; i < wpaicgTinymceEditorMenus.length;i++){
                let wpaicgTinymceEditorMenu = wpaicgTinymceEditorMenus[i];
                if(typeof wpaicgTinymceEditorMenu.name !== "undefined" && wpaicgTinymceEditorMenu.name !== '' && typeof wpaicgTinymceEditorMenu.prompt !== 'undefined' && wpaicgTinymceEditorMenu.prompt !== '') {
                    wpaicg_menus.push({
                        text: wpaicgTinymceEditorMenu.name,
                        onclick: function () {
                            let selected_html = editor.selection.getContent({
                                'format': 'html'
                            });
                            let selected_text = editor.selection.getContent({
                                'format': 'text'
                            });
                            if (selected_text === '') {
                                alert('Please select text');
                            } else {
                                wpaicgSendEditorPrompt(selected_text,selected_html, wpaicgTinymceEditorMenu.prompt, editor);
                            }
                        }
                    })
                }
            }
        }
        editor.addButton( 'wpaicgeditor', {
            title: 'AI Power',
            image: wpaicg_plugin_url+'public/images/logo.png',
            icon: false,
            type: 'menubutton',
            menu: wpaicg_menus
        });
    });
    function wpaicgSendEditorPrompt(text, html, prompt, editor){
        prompt = prompt.replace('[text]', text);
        let dom = tinymce.activeEditor.dom;
        let $ = tinymce.dom.DomQuery;
        let selectionRange = editor.selection.getRng();
        const loadingSpinnerId = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        if (typeof wpaicgEditorChangeAction === 'undefined') {
            wpaicgEditorChangeAction = 'below';
        }
        jQuery.ajax({
            url: wpaicg_editor_ajax_url,
            data: {action: 'wpaicg_editor_prompt', prompt: prompt,nonce: wpaicg_editor_wp_nonce},
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function (){
                if (wpaicgEditorChangeAction === 'below') {
                    let selectedNode = editor.selection.getEnd();
                    let spinnerHtml = createLoadingSpinner(
                        selectedNode,
                        wpaicgEditorChangeAction,
                        loadingSpinnerId,
                    )
                    let spinnerDom = $(spinnerHtml)[0];

                    let parentNode = selectionRange.endContainer.parentNode;
                    // if parent node is li then we need to create a new li
                    if (parentNode.tagName.toLowerCase() === 'li') {
                        $(selectedNode).after(spinnerDom);
                    } else if (selectedNode.textContent) {
                        selectionRange.collapse(false);
                        selectionRange.insertNode(spinnerDom);
                        editor.selection.collapse();
                    } else {
                        $(selectedNode).after(spinnerDom);
                    }
                }
                else { // above
                    let selectedNode = editor.selection.getStart();
                    let spinnerHtml = createLoadingSpinner(
                        selectedNode,
                        wpaicgEditorChangeAction,
                        loadingSpinnerId,
                    )
                    let spinnerDom = $(spinnerHtml)[0];

                    let parentNode = selectionRange.startContainer.parentNode;
                    // if parent node is li then we need to create a new li
                    if (parentNode.tagName.toLowerCase() === 'li') {
                        $(selectedNode).before(spinnerDom);
                    } else if (selectedNode.textContent) {
                        selectionRange.collapse(true);
                        selectionRange.insertNode(spinnerDom);
                        editor.selection.collapse();
                    } else {
                        $(selectedNode).before(spinnerDom);
                    }
                }
                editor.selection.collapse();
            },
            success: function (res){
                let spinner = dom.select('#' + loadingSpinnerId);
                if(res.status === 'success') {
                    let dataContent = res.data;
                    if (wpaicgEditorChangeAction === 'below') {
                        dataContent = '<br>' + dataContent;
                    }
                    else{
                        dataContent = dataContent + '<br>';
                    }
                    $(spinner).removeAttr('class');
                    $(spinner).removeAttr('id');
                    $(spinner).html(dataContent);
                }
                else{
                    $(spinner).remove();
                    alert(res.msg);
                }
            }
        })
    }
})();

