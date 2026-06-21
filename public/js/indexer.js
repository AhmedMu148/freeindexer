$(document).ready(function () {
    $("#indexer-form").on("submit", function (e) {
        e.preventDefault();
        var urls = $("#user_urls").val();
        var token = $('input[name="_token"]').val();

        $.post(
            "/indexer/submit",
            {
                user_urls: urls,
                _token: token,
            },
            function (response) {
                if (response.status === "start") {
                    $.post(
                        "/indexer/process",
                        {
                            urls: urls,
                            _token: token,
                        },
                        function (processResult) {
                            // اعرض النتائج للمستخدم
                            console.log(processResult);
                            // مثال: $('#indexer-results').html(JSON.stringify(processResult));
                        }
                    );
                } else {
                    alert(response.message);
                }
            }
        ).fail(function (xhr) {
            alert(xhr.responseJSON?.message || "Error");
        });
    });
});
