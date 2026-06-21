{{-- ✅ Chat without Tailwind: fully scoped CSS, won’t affect Filament --}}
<style>
  /* == Scope to #fi-chat only == */
  #fi-chat {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #fff;
    padding: 16px;
    max-height: 520px;
    overflow-y: auto;
  }

  #fi-chat .date-sep {
    display: flex;
    justify-content: center;
    margin: 8px 0 16px;
  }

  #fi-chat .date-badge {
    font-size: 11px;
    color: #4b5563;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    padding: 4px 10px;
    border-radius: 999px;
  }

  #fi-chat .row {
    display: flex;
    gap: 12px;
    margin-bottom: 18px;
    align-items: flex-start;
  }

  #fi-chat .row.left {
    justify-content: flex-start;
  }

  #fi-chat .row.right {
    justify-content: flex-end;
  }

  #fi-chat .row.right .inner {
    flex-direction: row-reverse;
  }

  #fi-chat .inner {
    display: flex;
    gap: 12px;
    max-width: 100%;
  }

  #fi-chat .avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    box-shadow: 0 1px 2px rgba(0, 0, 0, .06);
    flex: 0 0 auto;
  }

  /* #fi-chat .avatar.user {
    background: #2563eb;
  } */

  /* أزرق */
  /* #fi-chat .avatar.support {
    background: #10b981;
  } */

  /* أخضر */

  #fi-chat .block {
    min-width: 0;
    max-width: 75%;
  }

  #fi-chat .name {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .02em;
    margin-bottom: 4px;
  }

  #fi-chat .name.user {
    color: #4b5563;
    text-align: left;
  }

  #fi-chat .name.support {
    color: #0ea5a3;
    text-align: right;
  }

  #fi-chat .bubble {
    border-radius: 16px;
    padding: 10px 12px;
    font-size: 14px;
    line-height: 1.6;
    box-shadow: 0 1px 2px rgba(0, 0, 0, .06);
    word-break: break-word;
    white-space: pre-wrap;
  }

  #fi-chat .left .bubble {
    background: #eaf2ff;
    border: 1px solid #dbeafe;
    color: #1f2937;
  }

  /* user */
  #fi-chat .right .bubble {
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    color: #111827;
  }

  /* support */

  #fi-chat .meta {
    margin-top: 4px;
    font-size: 11px;
    color: #6b7280;
  }

  #fi-chat .meta.user {
    text-align: left;
  }

  #fi-chat .meta.support {
    text-align: right;
  }
</style>

<div id="fi-chat">
  @php $lastDate = null; @endphp

  @forelse($chat ?? [] as $m)
    {{-- فاصل التاريخ --}}
    @if(($m['date'] ?? '') !== $lastDate)
      @php $lastDate = $m['date'] ?? ''; @endphp
      <div class="date-sep">
        <span class="date-badge">{{ $m['date'] }}</span>
      </div>
    @endif

    @php
      $isUser = !empty($m['me']);  // true = اليوزر (شمال), false = السبورت (يمين)
      $sideClass = $isUser ? 'left' : 'right';
      $nameClass = $isUser ? 'user' : 'support';
      $avatarClass = $isUser ? 'user' : 'support';
    @endphp

    <div class="row {{ $sideClass }}">
      <div class="inner">
        <div class="avatar {{ $avatarClass }}">{{ $isUser ? '👤' : '🛡️' }}</div>

        <div class="block">
          <div class="name {{ $nameClass }}">
            {{ strtoupper($m['name'] ?? ($isUser ? 'YOU' : 'SUPPORT')) }}
          </div>

          <div class="bubble">{{ $m['body'] ?? '' }}</div>

          <div class="meta {{ $nameClass }}">{{ $m['date'] . ' - ' . $m['time'] ?? '' }}</div>
        </div>
      </div>
    </div>
  @empty
    <div class="date-sep"><span class="date-badge" style="background:#fff;">No messages yet.</span></div>
  @endforelse
</div>

<script>
  // Scroll to bottom
  document.addEventListener('DOMContentLoaded', () => {
    const box = document.getElementById('fi-chat');
    if (box) box.scrollTop = box.scrollHeight;
  });
</script>