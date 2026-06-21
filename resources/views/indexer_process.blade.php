@extends('layouts.app')

@section('content')

  <div class="container py-4">

    <div class="alert alert-success">
      Submitted URLs: {{ count(preg_split('/\r\n|\r|\n/', trim($urlsText))) }}
    </div>

    <div class="text-center btn-group-lg mb-4">
      <button class="btn btn-info btn-lg"><span>Total: </span><span id="total-count">0</span></button>
      <button class="btn btn-success btn-lg"><span>Success: </span><span id="success-count">0</span></button>
      <button class="btn btn-danger btn-lg"><span>Failed: </span><span id="failed-count">0</span></button>
    </div>

    <div class="progress" style="height:30px;">
      <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" style="width:0%;height:30px"></div>
    </div>

    <div class="card mt-3">
      <div class="card-header text-center">Results</div>
      <div class="card-body overflow-auto" id="card-block" style="max-height:400px;"></div>
      <div class="card-footer text-muted">Links indexer by <a href="{{ route('/') }}">FreeIndexer</a></div>
    </div>

    <div class="text-center mt-3 mb-3">
      <p>Link indexer provided by FreeIndexer, is a premium product for those who are working on the internet marketing,
        specially on the SEO Field.</p>
      <p>Our software in not only this online engine, FreeIndexer also offering a premium (live time, and monthly)
        subscription, you can <a href="{{ route('pricing') }}">check our plans and pricing</a></p>
      <p>This indexer engine is also provided as a Pro Desktop App, You can buy a live time license for $10, <a
          href="{{ route('buy-app') }}">check the Free Indexer App</a></p>
      <p>FreeIndexer “Audience Geography” showing that most of our website visitors comes from Brazil and United States,
        This info comes from Alexa “An Amazon.com company”</p>
    </div>

    <!-- start adsense -->
    <div class="ml-auto mr-auto">
      <script async
        src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4925041577244775"></script>
      <!-- freeindexer-process-auto -->
      <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-4925041577244775" data-ad-slot="9982802698"
        data-ad-format="auto" data-full-width-responsive="true"></ins>
      <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
      </script>
    </div>
    <!-- end adsense -->

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-round" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

  </div>

@endsection

@push('scripts')

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <script>
    if (!window.getCookie) {
      window.getCookie = function (k) {
        const m = document.cookie.match('(^|;)\\s*' + k + '\\s*=\\s*([^;]+)');
        return m ? decodeURIComponent(m.pop()) : "";
      };
    }
    if (!window.setCookie) {
      window.setCookie = function (name, value, days = 1) {
        const d = new Date();
        d.setTime(d.getTime() + days * 864e5);
        document.cookie = name + "=" + encodeURIComponent(value) + "; expires=" + d.toUTCString() + "; path=/";
      };
    }

    (function () {

      const template = (function () {
        const listType = @json($listType); // 'quick-list' | 'full-list'
        const isDomain = @json($isDomain); // true/false

        const domainFullFile = @json($domainFullFile);
        const deepFullFile = @json($deepFullFile);
        const domainQuickFile = @json($domainQuickFile);
        const deepQuickFile = @json($deepQuickFile);

        if (listType === 'full-list') return isDomain ? domainFullFile : deepFullFile;
        return isDomain ? domainQuickFile : deepQuickFile;
      })();

      const params = new URLSearchParams(window.location.search);
      const isContinue = params.get('continue') === '1';

      function clearIndexerCookies() {
        [
          'hash', 'filename',
          'java-total-count', 'user-urls-count',
          'total-urls-count', 'total-count',
          'progress-bar', 'success-count',
          'failed-count', 'completed'
        ].forEach(k => setCookie(k, '', -1));
      }

      if (!isContinue) {
        clearIndexerCookies();
      }

      let eachPageCount = 0;
      const state = {
        total: 0,
        success: 0,
        failed: 0,
        expected: 0
      };

      const savedTotal = parseInt(getCookie('total-count') || '0', 10);
      const savedSuccess = parseInt(getCookie('success-count') || '0', 10);
      const savedFailed = parseInt(getCookie('failed-count') || '0', 10);
      const savedExpected = parseInt(getCookie('total-urls-count') || '0', 10);

      if (!isNaN(savedExpected) && savedExpected > 0) {
        state.expected = savedExpected;
      }
      if (!isNaN(savedTotal) && savedTotal > 0) {
        state.total = savedTotal;
      }
      if (!isNaN(savedSuccess) && savedSuccess > 0) {
        state.success = savedSuccess;
      }
      if (!isNaN(savedFailed) && savedFailed > 0) {
        state.failed = savedFailed;
      }

      (function initialUI() {
        const percent = state.expected > 0 ?
          Math.min(100, Math.round((state.total / state.expected) * 100)) :
          0;
        $("#success-count").text(state.success);
        $("#failed-count").text(state.failed);
        $("#total-count").text(state.total + "/" + (state.expected || 0));
        $('.progress-bar').css("width", percent + "%");
      })();

      const existingHash = getCookie('hash');
      const existingTemplate = getCookie('filename');

      if (isContinue && existingHash && existingTemplate) {

        window.currentHash = existingHash;
        window.currentTemplate = existingTemplate;

        runLoop();

      } else {

        $.post("{{ route('indexer.submit') }}", {
          urls_text: @json($urlsText),
          template: template,
          _token: "{{ csrf_token() }}"
        }, function (resp) {
          if (resp.status !== 'start') {
            alert(resp.message || 'Error');
            return;
          }

          // console.log('DEBUG resp:', resp.hash);
          // console.log(resp.message);

          window.currentHash = resp.hash;
          window.currentTemplate = template;

          setCookie('filename', template, 1);
          setCookie('hash', resp.hash, 1);
          setCookie('total-urls-count', resp.total_expected, 1);
          setCookie('total-count', 0, 1);
          setCookie('success-count', 0, 1);
          setCookie('failed-count', 0, 1);
          setCookie('progress-bar', 0, 1);
          setCookie('completed', false, 1);

          state.expected = resp.total_expected;
          $('#total-count').text('0/' + state.expected);
          $('.progress-bar').css('width', '0%');

          runLoop();
        }).fail(function (xhr) {
          if (xhr.responseText) {
            try {
              const data = JSON.parse(xhr.responseText);
              if (data && data.message) message = data.message;
            } catch (e) {
              message = xhr.responseText;
            }
          }
          // const data = JSON.parse(xhr.responseText);
          // showErrorModal('Unknown error. Please contact support.');
          showErrorModal(message);
        });
      }

      function updateUI() {
        const percent = state.expected > 0 ?
          Math.min(100, Math.round((state.total / state.expected) * 100)) :
          0;

        $("#success-count").text(state.success);
        $("#failed-count").text(state.failed);
        $("#total-count").text(state.total + "/" + state.expected);
        $('.progress-bar').css("width", percent + "%");

        const cb = document.getElementById('card-block');
        if (cb) cb.scrollTop = cb.scrollHeight;

        setCookie('total-count', state.total, 1);
        setCookie('success-count', state.success, 1);
        setCookie('failed-count', state.failed, 1);
        setCookie('progress-bar', percent, 1);
      }

      function nextPage() {
        $('#myModal').modal('hide');
        const url = new URL("{{ url('/process') }}", window.location.origin);
        url.searchParams.set('continue', '1');
        window.location.href = url.toString();
      }
      window.nextPage = nextPage;

      // function nextPage() {
      //   $('#myModal').modal('hide');

      //   const url = new URL(window.location.href);

      //   window.location.href = url.toString();
      // }
      // window.nextPage = nextPage;

      function runLoop() {
        const hash = window.currentHash || getCookie('hash');
        const file = window.currentTemplate || getCookie('filename');
        if (!hash || !file) {
          setTimeout(runLoop, 200);
          return;
        }

        $.ajax({
          url: "{{ route('indexer.step') }}",
          method: "POST",
          dataType: "text",
          cache: false,
          data: {
            hash: hash,
            template: file,
            file: file,
            _token: "{{ csrf_token() }}"
          }
        }).done(function (result) {
          if (typeof result === 'string' && result.length && result[0] === '{') {
            try {
              const data = JSON.parse(result);
              if (data && data.status === 'wait') {
                const d = Math.max(50, parseInt(data.retry_after_ms || 200, 10));
                setTimeout(runLoop, d);
                return;
              }
            } catch (e) {
              // con
            }
          }

          if (result === "d:<STRONG>Done</STRONG>") {
            updateUI();
            const $m = $('#myModal');
            $m.find('.modal-body').html(
              '<div class="p-3 container text-center">' +
              '<h3 class="text-success">CONGRATULATIONS!</h3>' +
              '<p class="lead">You have finished processing all the URLs.</p>' +
              '<hr><p>Results :</p>' +
              '<div class="text-center mt-3" style="background:#f9f9f9;padding:20px">' +
              '<h5>Total Submissions = ' + state.total + '</h5>' +
              '<h5>Success = ' + state.success + '</h5>' +
              '<h5>Failed = ' + state.failed + '</h5>' +
              '</div></div>'
            );

            $m.find('.modal-footer').html(`
                                                          <button type="button" class="btn btn-danger btn-round" data-bs-dismiss="modal">Close</button>
                                                          <a href="{{ route('/') }}" class="btn btn-info btn-round">GO TO HOME PAGE</a>
                                                        `);

            clearIndexerCookies();
            $m.modal('show');
            return;
          }
          if (result.includes('Success') || result.includes('Failed')) {
            $("#card-block").append("<div class='px-3'>" + result + "</div><hr>");
            if (result.includes('Success')) state.success++;
            else state.failed++;
            state.total++;
            eachPageCount++;
          }
          updateUI();
          if (eachPageCount >= 100) {
            const $m = $('#myModal');
            $m.find('.modal-body').html(
              "<p class='text-success p-3'>You Have proceeded " + state.total + " of " + state.expected +
              "<br> To Continue Please Wait 5 Seconds Or Click on Next Button</p>"
            );
            $m.find('.modal-footer').html(`
                                  <button type="button" class="btn btn-danger btn-round" data-bs-dismiss="modal">Close</button>
                                  <button type="button" class="btn btn-info btn-round" onclick="nextPage()">Next</button>
                                `);
            $m.modal('show');
            setTimeout(nextPage, 5000);
            return;
          }
          setTimeout(runLoop, 200);
        }).fail(function (xhr) {
          console.error('STEP fail', xhr.status, xhr.responseText);
          setTimeout(runLoop, 500);
        });
      }
    })();

    function showErrorModal(message = 'Unknown error. Please contact support.') {
      const $m = $('#myModal');
      $m.find('.modal-footer .btn-info').remove();
      $m.find('.modal-body').html(`
                            <div class="p-3 text-center">
                              <h3 class="text-danger mb-3">Oops!</h3>
                              <p class="mb-1">${message}</p>
                              <p class="mt-2">Support: <a href="mailto:support@freeindexer.com">support@freeindexer.com</a></p>
                            </div>
                          `);
      $m.modal('show');
    }


  </script>

@endpush