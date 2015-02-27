/**
 * ownCloud - youtubedl
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Guven Atbakan <guvenatbakan@gmail.com>
 * @copyright Guven Atbakan 2014
 */

(function ($, OC) {

	$(document).ready(function () {

		$('#download').click(function () {
            $('#echo-debug-content').text('');
            $('#echo-debug-content').slideUp();
            $('#echo-debug-button').hide();

            $('#echo-result').text('File is downloading, please wait...');
			var url = OC.generateUrl('/apps/youtubedl/download');
			var data = {
				url: $('#url').val(),
				mp3: $('#mp3:checked').val(),
                dir: $('#dir').val()
			};

			$.post(url, data).success(function (response) {
                $('#echo-result').text('');
				$('#echo-result').append("<strong>Download Result:</strong>"+response.status);
				$('#echo-result').append("<br/>"+response.message);
				$('#echo-result').append("<br/><strong>URL:</strong>"+response.url);
                $('#url').val('');

                $('#echo-debug-button').show();
                $(response.output).each(function (i) {
                    $('#echo-debug-content').append(response.output[i]);
                    $('#echo-debug-content').append("<br/>");
                });

			});
			
		});
        $('#updateLink').click(function () {
            $('#echo-debug-content').text('');
            $('#echo-debug-content').slideUp();
            $('#echo-debug-button').hide();

            $('#echo-result').text('Youtube-dl updating, please wait...');
			var url = OC.generateUrl('/apps/youtubedl/updateyoutubedl');
			var data = {

			};

			$.post(url, data).success(function (response) {
                $('#echo-result').text('');
				$('#echo-result').append("<strong>Update Result:</strong>"+response.status);
				$('#echo-result').append("<br/>"+response.message);

                $('#echo-debug-button').show();
                $(response.output).each(function (i) {
                    $('#echo-debug-content').append(response.output[i]);
                    $('#echo-debug-content').append("<br/>");
                });

			});

		});

        $('#showDebug').click(function(){
            $('#echo-debug-content').slideDown();
        });
	});

})(jQuery, OC);