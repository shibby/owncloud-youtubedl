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
			});
			
		});
	});

})(jQuery, OC);