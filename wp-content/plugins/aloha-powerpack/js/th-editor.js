/**
 * Created by rl on 2017-03-30.
 */
(function ($) {

    $.fn.setupTHMVAccordion = function (options) {
        var $parentElement = $(this);
        var accordionClass = '.accordion-holder';

        if ($parentElement.find(accordionClass).length) {
            return false;
        }
        var accordionHolderClass = '.accordion-holder-parent';
        var accordionElementClassWithoutDot = accordionHolderClass.replace('.', '');
        var accordionClassWithoutDot = accordionClass.replace('.', '');
        var accordionContentClass = '.accordion-content';
        var accordionContentClassWithoutDot = accordionContentClass.replace('.', '');
        var elementorControlClassPrefix = 'elementor-control-';
        var elementorRepeaterClass = '.elementor-repeater-row-controls';

        

        // Default options
        var settings = $.extend(true, {
            accordionTitlePrefix: 'Tab',
            contentElements: [],
            orderingField: 'thmv_tab_ordering'
        }, options);
        var firstElement = settings.contentElements[0];
        var ordering_element = '.elementor-control-'+settings.orderingField;
        var ordering = $parentElement.find(ordering_element).find('input').val();


        var init = function () {
            var copy_element = '.'+elementorControlClassPrefix+firstElement + '0';
            
            $parentElement.find(ordering_element).hide();
            $parentElement.find(copy_element).each(function () {
                var $parent = $(this).closest(elementorRepeaterClass);
                var $accordionHolder = $('<div class="' + accordionElementClassWithoutDot + '"/>');
                $(this).before($accordionHolder);
                
                var $testElement = $parent.find('div[class*="'+elementorControlClassPrefix+firstElement+'"]');
                var lengthArr = $testElement.length;
                if (ordering === '') {
                    var orderArr = Array(lengthArr).fill().map((_, i) => i);
                }
                else {
                  var orderArr = ordering.split(',');  
                }
                

                var contentElementsArray = [];
                for(var j=0; j<settings.contentElements.length; j++){
                    contentElementsArray[j] = $parent.find('div[class*="'+elementorControlClassPrefix+settings.contentElements[j]+'"]');
                }
                for (var i = 0; i < orderArr.length; i++) {
                    
                    var order = orderArr[i];
                    var holder = $('<div class="' + accordionClassWithoutDot + '"/>');
                    var innerholder = $('<div class="' + accordionContentClassWithoutDot + '"/>');
                    $accordionHolder.append(holder);
                    holder.append('<h3><span class="title">' +settings.accordionTitlePrefix+' '+ (i + 1) + '</span><span class="order-arrows"><span data-action="up" class="order-up fas fa-chevron-up"></span><span data-action="down" class="order-down fas fa-chevron-down"></span></span>');
                    holder.append(innerholder);
                    
                    
                    for(var j=0; j<contentElementsArray.length; j++){
                        var $tempElement = contentElementsArray[j].eq(order);
                        innerholder.append($tempElement);
                    }
                    
                }

            });
        }

        init();
        
        $(this).find(accordionClass).find('h3 .title').on('click', function (e) {
            e.preventDefault();
            $(this).closest(accordionClass).find(accordionContentClass).toggle();
        });
        function onMoveComplete() {
            var valueArr = [];
            $parentElement.find(accordionHolderClass).find('input[data-setting*="'+firstElement+'"]').each(function () {
                var tempSetting = $(this).data('setting');
                valueArr.push(tempSetting.replace(firstElement, ''));
            });
            var value = valueArr.join();
            var orderingField = $parentElement.find(ordering_element).find('input');
            orderingField.val(value);
            orderingField.trigger('input');
        }

        $(this).find('.order-arrows > span').on('click', function (e) {
            e.preventDefault();

            var $thisTab = $(this).closest(accordionClass);
            var $holder = $thisTab.closest(accordionHolderClass);
            $thisTab.find(accordionContentClass).hide();
            var thisIndex = $thisTab.index();
            var action = $(this).data('action');
            if (thisIndex > 0 && action === 'up') {
                var $previousDiv = $holder.find(accordionClass).eq(thisIndex - 1);
                if ($previousDiv.length) {
                    $previousDiv.before($thisTab);
                }

            } else if (action === 'down') {
                var $nextDiv = $holder.find(accordionClass).eq(thisIndex + 1);
                if ($nextDiv.length) {
                    $nextDiv.after($thisTab);
                }


            }
            onMoveComplete();
        });

        return this;

    };

}(jQuery));
jQuery(function ($) {
    if (themo_editor_object.check_for_woocommerce_checkout_error) {
        /** check for the error, if no item in cart, there will be an error **/
        var woocommerce_check_interval = 100;//100ms
        var woocommerce_check_max_count = 10000 / woocommerce_check_interval;//10 secs

        var woocommerce_check_current = 0;
        var woocommerce_interval = setInterval(function () {
            woocommerce_check_current++;
            if ($('#elementor-fatal-error-dialog').length) {
                clearInterval(woocommerce_interval);
                var dlg_id = 'woocommerce-elementor-fatal-error-dialog';
                var args = {
                    headerMessage: themo_editor_object.woocommerce_checkout_error_strings.header,
                    message: themo_editor_object.woocommerce_checkout_error_strings.message,
                    id: dlg_id
                };
                elementor.showFatalErrorDialog(args);
                var $dlg = $('#' + dlg_id);
                $dlg.css('z-index', '99999');
                $dlg.find('.dialog-ok').hide();
                $dlg.find('.dialog-confirm-cancel').css('width', '100%');
                $('#elementor-try-safe-mode').css('z-index', '999999');
                return;
            } else if (woocommerce_check_current >= woocommerce_check_max_count) {
                clearInterval(woocommerce_interval);
            }
        }, 100);
    }
    
    
    var interval = false;
    var thmv_repeater_editable = '#elementor-controls > .elementor-control-type-repeater .elementor-repeater-row-controls.editable';
    var thmv_style_element = 'select[data-setting="thmv_style"]';
    var styleToHideIconsFor = ['style_4','style_5'];
    var interval_tabs = false;
    $('body').addClass(themo_editor_object.elementor_theme_ui);
    $('body').addClass(themo_editor_object.active_theme);
    
   elementor.settings.editorPreferences.addChangeCallback('ui_theme', function (newValue) {
       if(newValue!==''){
           const newClass = themo_editor_object.elementor_theme_ui_class_pattern.replace("%s", newValue);
           $('body')[0].className = $('body')[0].className.replace(/themo-elementor-[a-z]+(-mode)/g, newClass);
           elementor.settings.editorPreferences.onUIThemeChanged(newValue);
       }
   });     
    elementor.settings.page.addChangeCallback('themo_button_style', function (newValue) {
       //load new css
      $cssLink = elementorFrontend.elements.$head.find('#'+themo_editor_object.aloha_button_style_id+'-css');
      const newLink = $cssLink.attr('href').replace(/button-styles-[a-z]+/g, themo_editor_object.aloha_button_style_prefix+newValue);    
      $cssLink.attr('href', newLink);
   }); 

    function moveTHMVSectionTOTop(){
        for (var i=themo_editor_object.elementor_single_elementor_slug.length; i>= 0; i--){
            var single_template_class = '#elementor-panel-category-'+themo_editor_object.elementor_single_elementor_slug[i];
            if($(single_template_class+':not(.has-moved)').length){
                $(single_template_class).addClass('has-moved').prependTo('#elementor-panel-categories');
            }
        }
        
    }
    
    
    if (typeof $e != "undefined") {
        if(themo_editor_object.elementor_is_single_template!= "undefined"){
            setInterval(moveTHMVSectionTOTop, 200);
        }
        
        
        elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {
            if ('themo-accommodation-listing' !== model.elType) {
                clearInterval(interval);
                return;
            }
            if ('themo-tabs' !== model.elType) {
                clearInterval(interval_tabs);
                return;
            }

        });
        elementor.hooks.addAction('panel/open_editor/widget/themo-accommodation-listing', function (panel, model, view) {
            interval = setInterval(function () {

                if ($(thmv_repeater_editable).length) {

                    var listing_style = view.container.settings.attributes.thmv_style;

                    $(thmv_repeater_editable).find(thmv_style_element).each(function () {
                        if ($(this).val() !== listing_style) {
                            $(this).val(listing_style).trigger('change');
                        }
                    });
                    if(styleToHideIconsFor.indexOf(listing_style)>-1){
                        $(thmv_repeater_editable).find('.elementor-control-type-tab.elementor-control-icons').hide();
                    }
                    else {
                        $(thmv_repeater_editable).find('.elementor-control-type-tab.elementor-control-icons').show();
                        $(thmv_repeater_editable).setupTHMVAccordion({
                            accordionTitlePrefix: 'Icon',
                            contentElements: ['thmv_icon_icon', 'thmv_icon_label'],
                            orderingField: 'thmv_icon_ordering'
                        });

                        /** check if the child fields are hidden, if so, hide the accordion too **/
                        if ($(thmv_repeater_editable).find('.elementor-control-thmv_icon_icon0').hasClass('elementor-tab-close')) {
                            $(thmv_repeater_editable).find('.accordion-holder').hide();
                        } else {
                            $(thmv_repeater_editable).find('.accordion-holder').show();
                        }
                    }
                   
                }

            }, 100);


        });

        elementor.hooks.addAction('panel/open_editor/widget/themo-tabs', function (panel, model, view) {
            interval_tabs = setInterval(function () {

                if ($(thmv_repeater_editable).length) {
                    $(thmv_repeater_editable).setupTHMVAccordion({
                            contentElements : ['thmv_tab_item_title','thmv_tab_item_price','thmv_tab_item_content']
                        });
                }

            }, 100);


        });
        //console.log("Loading Page Settings Panel");

        // Page Layout Options
        elementor.settings.page.addChangeCallback('themo_page_layout', function (newValue) {
            // Here you can do as you wish with the newValue
            //console.log("themo_page_layout");

            try {
                //code that causes an error
                $e.run('document/save/auto', {
                    force: true,
                    onSuccess: function () {
                        elementor.reloadPreview();
                        elementor.once('preview:loaded', function () {
                            $e.route('panel/page-settings/settings')
                        }
                        )
                    }
                });

            } catch (e) {
                console.log("Failed to update Page Settings.");
            }

        });
        // switch kits
        elementor.settings.page.addChangeCallback('aloha-active-kit', function (newValue) {
        //console.log(elementor.settings.page.model.get('aloha-active-kit'));
            $('#elementor-loading').show();
            var old_kit = false;
            $.post(themo_editor_object.ajaxurl, {action: 'aloha_get_old_kit_name'}).done(function (result) {
                old_kit = result.kit;
                if (old_kit != newValue) {
                    $.post(themo_editor_object.ajaxurl, {action: 'aloha_switch_kits', old_kit: old_kit, new_kit: newValue}).done(function (result) {
                        if (result.success) {
                            const reg = /&active-document=\d*/;
                            var new_location = window.location.href;
                            new_location = new_location.replace(reg, "");
                            window.location.replace(new_location);
                        } else {
                            console.log('error switching kits');
                        }

                    });
                }
            });
        });
        // restore kit
        elementor.channels.editor.on('aloha_restore_backup_event', function () {
            if (confirm(themo_editor_object.kit_restore_confirmation) == true) {

                var $title = $('#elementor-loading').find('.elementor-loading-title');
                $title.text(themo_editor_object.restore_text);
                $('#elementor-loading').show();
                $.post(themo_editor_object.ajaxurl, {action: 'aloha_restore_backup'}).done(function (result) {
                    if (result.success) {
                        const reg = /&active-document=\d*/;
                        var new_location = window.location.href;
                        new_location = new_location.replace(reg, "");
                        window.location.replace(new_location);
                    } else {
                        console.log('error restoring backup');
                        $('#elementor-loading').hide();
                    }
                });
            }
        });
        // Header Transparency
        elementor.settings.page.addChangeCallback('themo_transparent_header', function (newValue) {
            // Here you can do as you wish with the newValue

            //onsole.log("themo_transparent_header");

            try {
                //code that causes an error
                $e.run('document/save/auto', {
                    force: true,
                    onSuccess: function () {
                        elementor.reloadPreview();
                        elementor.once('preview:loaded', function () {
                            $e.route('panel/page-settings/settings')
                        }
                        )
                    }
                });

            } catch (e) {
                console.log("Failed to update Page Settings.");
            }


        });

        // Header Contenet Style
        elementor.settings.page.addChangeCallback('themo_header_content_style', function (newValue) {
            // Here you can do as you wish with the newValue

            //console.log("themo_header_content_style");

            try {
                //code that causes an error
                $e.run('document/save/auto', {
                    force: true,
                    onSuccess: function () {
                        elementor.reloadPreview();
                        elementor.once('preview:loaded', function () {
                            $e.route('panel/page-settings/settings')
                        }
                        )
                    }
                });

            } catch (e) {
                console.log("Failed to update Page Settings.");
            }

        });

        // Alt Logo
        elementor.settings.page.addChangeCallback('themo_alt_logo', function (newValue) {
            // Here you can do as you wish with the newValue

            //console.log("themo_alt_logo");

            try {
                //code that causes an error
                $e.run('document/save/auto', {
                    force: true,
                    onSuccess: function () {
                        elementor.reloadPreview();
                        elementor.once('preview:loaded', function () {
                            $e.route('panel/page-settings/settings')
                        }
                        )
                    }
                });

            } catch (e) {
                console.log("Failed to update Page Settings.");
            }
        });
    }
});