define([
    'jquery',
    'mage/template',
    'jquery/ui',
    'mage/validation'
], function ($, mageTemplate, url){
    "use strict";

    $.widget('mage.lookingfor', {
        options: {
            formname : "#form-lookingforform",
            resultContainer: "#result-lookingforform",
            url: url
        },

        _create: function () {
            this._search();
        },

        /**
         * Method triggers an AJAX request to make API query
         * @private
         */
        _search: function () {
            var self = this;
            var dataForm = $(self.options.formname);
            dataForm.mage('validation', {});
            $(self.options.resultContainer).empty();

            $('.lookingfor-action').click(function(event) {

                var status = dataForm.validation('isValid'); //validates form and returns boolean
                if(status) {
                    var param = dataForm.serialize();
                    $("#lookingfor-ajax-overlay").addClass('ajax-loading');
                    $.ajax({
                        url: self.options.url,
                        type: 'POST',
                        dataType: 'json',
                        data: param,
                        //showLoader: true
                    }).done(function (data) {
                        if (data.result) {
                            $(self.options.resultContainer).html('<strong>' + data.message + '</strong>');
                        } else {
                            $(self.options.resultContainer).html('<strong class="error-lookingforform">' + data.message + '</strong>');
                        }
                        $("#lookingfor-ajax-overlay").removeClass('ajax-loading');
                        //showLoader: false
                    }).fail(function (response) {
                        $(self.options.resultContainer).html('<strong class="error-lookingforform">Error</strong>');
                        $("#lookingfor-ajax-overlay").removeClass('ajax-loading');
                        //showLoader: false
                    });
                }
            });
        }

    })

    return $.mage.lookingfor;
});
