const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const product = urlParams.get('page');

(function ($) {
  $(document).ready(function () {

    if (product == "add-new-video") {
      const dropArea = document.querySelector('#dropContainer');
      const input = document.querySelector('#fileInput');


      input.addEventListener('change', videoUploadAction);
      dropArea.addEventListener('drop', videoUploadAction);

      function videoUploadAction() {

        document.getElementById("custom-bg-grey-containter").style.display = "block";
        document.getElementById("teyuto-progress-bar").style.display = "flex";
        if (dropArea.classList.contains("currentUpload")) {
          return;
        }
        document.getElementById("dropContainer").classList.add("currentUpload");
        document.getElementById("fileInput").disabled = true;


        var file = input.files[0];
        var fileName = input.files[0].name;

        if (file) {
          var headers = {
            "Authorization": $('#teyuto_apiKey').val(),
            "channel": $('#teyuto_channel').val(),
          };
          var formdata = {
            "title": fileName
          };
          $.ajax({
            url: "https://api.teyuto.tv/v2/videos/vod",
            type: "POST",
            headers: headers,
            processData: false,
            contentType: false,
            data: JSON.stringify(formdata),
            success: function(json) {
              var dataVideo = JSON.parse(json);

              $.ajax({
                type: "GET",
                url:  'https://api.teyuto.tv/v2/videos/' + dataVideo.id + '/signed/upload',
                headers: headers,
                cache: false,
                success: function (json) {
                    let data = JSON.parse(json);
                    var upload = new tus.Upload(file, {
                        endpoint: `https://${data.hostname}/upload/`,
                        autoRetry: true,
                        retryDelays: [0, 3000, 5000, 10000, 20000],
                        chunkSize: 10000000,
                        metadata: {
                            filename: data.video_name,
                            token: data.token,
                            video_id: data.video_id,
                            client_id: data.client_id
                        },
                        onSuccess: function() {
                          document.getElementById("video-information").innerHTML = "Watch the video <a href='admin.php?page=teyuto-library&id=" + dataVideo.id + "'>here</a>";
                          document.getElementById("fileInput").value = null;
                          document.getElementById("fileInput").disabled = false;
                          dropArea.classList.remove("currentUpload");
                          document.getElementById("custom-bg-grey-containter").style.display = "none";
                        },
                        onProgress: function(bytesUploaded, bytesTotal) {
                          var percentage = (bytesUploaded / bytesTotal * 100).toFixed(2);
                          document.getElementById("progress-tracker").value = percentage;
                          document.getElementById("chunk-information").innerHTML = "Total uploaded: " + percentage + "%";
                        },
                        onError: function(){
                          document.getElementById("video-information").innerHTML = "An error occured. Please refresh the page and try again.";
                        }
                    });
                    upload.start();
                  }
              });
            },
            error: function(xhr, status, error) {
                console.error('error', error);
                document.getElementById("video-information").innerHTML = "An error occured. Please refresh the page and try again.";
            }
            });
        }
     
      }
    }

    $("#library-search-value").on("change", function () {
      var searchValue = $(this).val();
      var currentUrl = window.location.href;
      var updatedUrl = currentUrl + (currentUrl.indexOf('?') !== -1 ? '&' : '?') + 'paget=1&searcht=' + encodeURIComponent(searchValue);
      window.location.href = updatedUrl;
    });

    $(".button-form-trigger").on("click", function () {
      $(this).parent().parent().find('#head-form').find('#form-button-trigger').trigger('click');
    });

    if ($(".video-info-frame").hasClass("active")) {
      $("#bg-for-videos").css("display", "block");
    } else {
      $("#bg-for-videos").css("display", "none");
    }

    $(".clickable-item").on("click", function () {

      idForItemItem = $(this).prev();

      var $temp = $("<input>");
      $("body").append($temp);
      $temp.val(idForItemItem.val()).select();
      document.execCommand("copy");
      $temp.remove();

      $(this).prev().focus();
      $(this).prev().select();
    });

    function loadCurrentVideoPlayer() {
      var url = $(".video-info-frame.active .custom-iframe input").val();

      if (!url) {
        return;
      }

      var iframe = document.createElement("iframe");
      iframe.src = url;
      iframe.width = "100%";
      iframe.height = "100%";
      iframe.frameborder = "0";
      iframe.scrolling = "no";
      iframe.allowfullscreen = "";

      $(".video-info-frame.active .custom-iframe").append(iframe);
    }

    loadCurrentVideoPlayer();

    $(".teyuto-trig").click(function () {
      $(this).prev().addClass("active");
      $("#bg-for-videos").css("display", "block");
      loadCurrentVideoPlayer();
    });

    $(".close-video-trig").on("click", function () {
      $("#bg-for-videos").css("display", "none");
      $(".video-info-frame").removeClass("active");
      $(this).parent().parent().next().find("iframe").attr('src', $(this).parent().parent().next().find("iframe").attr('src'));
    });

    $("#bg-for-videos").on("click", function (e) {
      $(this).next().next().find(".video-info-frame.active").find("iframe").attr('src', $(this).next().next().find(".video-info-frame.active").find("iframe").attr('src'));
      if (e.target !== this) { }
      else {
        $("#bg-for-videos").css("display", "none");
        $(".video-info-frame").removeClass("active");
      }
    });
  });
})(jQuery);


function customfunction() {
  if (confirm("You are about to permanently delete this item from your site. This action cannot be undone. 'Cancel' to stop, 'OK' to delete.")) {
  } else {
    return false;
  }
}

function teyuto_change_url() {
  window.history.pushState("object or string", "Title", "admin.php?page=teyuto-library");
}