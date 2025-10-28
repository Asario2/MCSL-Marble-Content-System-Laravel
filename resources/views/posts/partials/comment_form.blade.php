
@php
    $openComments = isset($_GET['open_comments']) ? $_GET['open_comments'] : '';
    $opc = rawurldecode($openComments);
    $opc = explode(',', $opc);

    $disp = in_array($post->id, $opc) ? 'block' : 'none';
    if (empty($opc[0]) || $opc[0] == 'undefined') {
        $disp = 'none';
    }
    if (isset($comments[$post->id]) && count($comments[$post->id]) > 0) {
        $cdisp = 'block';
    } else {
        $cdisp = 'none';
    }
    if (!session_id()) {
        session_start();
    }
    $cid = 1;

$cids = $cids ?? [];
@endphp
@if (session('success_'.$post->id))
                <div class="alert alert-success">
                    {{ session('success_'.$post->id) }}
                </div>
            @endif
<div id='form_com_{{$post->id}}' style='display:{{$disp}}'>
    <input type='hidden' name='post_id' value='{{ $post->id }}'>
    <div class="comment-box-container" id="comment-box-{{ $post->id }}" style='display:{{ $cdisp }};'>
        <div class="comments-content" id="comments-content-{{ $post->id }}" data-post-id="{{ $post->id }}"
            style="overflow-y: auto;max-height:340px">
            <!-- Existierende Kommentare -->

            @if (isset($comments[$post->id]) && count($comments[$post->id]) > 0)
                @php
                    if (!isset($_SESSION['comment_ids'][$post->id]) || !is_array($_SESSION['comment_ids'][$post->id])) {
                        $_SESSION['comment_ids'][$post->id] = [];
                    }
                    foreach ($comments[$post->id] as $comment) {
                        // Logge, ob der Kommentar bereits verarbeitet wurde
                        if (in_array($comment->id, $_SESSION['comment_ids'][$post->id])) {
                           // \Log::info('Skipping comment ID (already processed): ' . $comment->id);
                            //continue;
                        }

                        // Füge die Kommentar-ID zur Session hinzu
                        $_SESSION['comment_ids'][$post->id][] = $comment->id;
                        $cids[$post->id] = $comment->id;

                        $segment = request()->segment(2); // Liefert das 2. Segment der URL
                        $comment->image_path = $comment->image_path ?? "008.jpg";
                    @endphp

                        {{-- Kommentar rendern --}}
                        <div class="comment-bubble-container" id="comment-{{$post->id}}-{{$comment->id}}">
                            <img src="/images/_ab/users/sm/{{ $comment->image_path }}" alt="{{ $comment->name }}" class="user-icon">
                            <div class="comment-bubble">
                                <span class='smtext'>{{ $comment->name ?: 'Unbekannt' }}</span><br />
                                <span class='mad'>{!! nl2br(e($comment->content)) !!}</span>
                                @if (CheckRights(Auth::id(),"comments","delete"))
                                <input type='hidden' id='is_delable' value="true">
                                <form action="{{ route('comment.delete',[$post->id, $comment->id,$table,$segment]) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type='submit' class='trans-btn' onclick="return confirm('Möchten Sie diesen Eintrag wirklich löschen?');"><i class='fa fa-del-icon'></i></button>
                            </form>
                            @endif
                            </div>

                        </div>
                        {{--
                        <span class='smtext'>
                        <div class="container mt-4">
                            <div class="row">
                                <div class="col-md-4">
                                {!!ageofArticle($comment->created_at)!!}
                                </div>
                                 <div class="col-md-4">
                                <a href=''>Antworten</a>
                                </div>
                                <div class="col-md-4">
                                    </div>
                            </div>
                            </div>
                            </span>
                            --}}
                    @php
                    }
                        // \Log::info('Added to session: ' . json_encode($cid));
                 @endphp
            @endif


            {{-- @if (isset($comments[$post->id]) && count($comments[$post->id]) > 0)
                @php
                    foreach ($comments[$post->id] as $comment) {
                        if (in_array($comment->id, $_SESSION['comment_ids'])) {
//                             \Log::info('burned:' . $comment->id);
                            continue; // Überspringe den Kommentar
                        }
                        // Füge die ID des aktuellen Kommentars zur Session hinzu
//                         \Log::info('Session before: ' . json_encode($_SESSION['comment_ids']));
                        $_SESSION['comment_ids'][] = $comment->id;
//                         \Log::info('Session after: ' . json_encode($_SESSION['comment_ids']));
                        //\Log::info('cid fin:' . $comment->id);
                    @endphp
                        @include 'posts.partials.comment_box',['comments'=>$comments];
                    @php
                    }
                @endphp



            @endif --}}




            <div id='com-btn-{{$post->id}}'>
            </div>
            <div id='loading-{{$post->id}}' style='display:none;'></div>
            <script>
                // var take = 10; // Anzahl der Kommentare pro Anfrage
                // var offset = 0; // Start bei 0
                // var hasMore = true; // Gibt es noch weitere Kommentare?
                // var {{$post->id}} = {{ $post->id }}; // ID des aktuellen Posts
            </script>
        </div>

    @php
        $user = Auth::user();
        // \Log::info($_SESSION['comment_ids']);
        // $_SESSION['comment_ids'] = [];

    @endphp
    <form action="{{ route('comments.store', ['table' => $table, 'id' => $post->id]) }}" id='fcom_{{$post->id}}'
    method="POST">
    @csrf
    <div class="comment-input-container">
<input type='hidden' name='post_id' value='{{$post->id}}'>
        <div class='u-icon'>
            <span style='height:18px;'></span><br /><br />
            <img src='/images/users/thumbs/{{ $user->image_path ?? '008.jpg' }}' class='uicon'>
        </div>&nbsp;&nbsp;
        <div class="input-bubble">
            <input type="text" class="comment-input" id="comment-input-{{ $post->id }}"
                placeholder="Dein Kommentar..." maxlength="100" name="comment" autocomplete="off"
                oninput="sendcmd({{ $post->id }});checkInputLength({{ $post->id }})" size='32' />
            <textarea class="commentTextarea" id="comment-textarea-{{ $post->id }}" placeholder="Dein Kommentar..."
                oninput="sendcmd({{ $post->id }});checkInputLength({{ $post->id }});" name="comment2" cols="60"
                rows="4" style="display: none;" maxlength="1000"></textarea>
            {{-- <button class='btn btn-sm con_sub hidden'
                onclick="$('#form_com_{{ $post->id }}').submit();return false;">Senden</button> --}}
        </div>

    </div>

</form>

</div>
@php
   echo "<script>
                           scrollComments(".$post->id.");
                       </script>";
   // \Log::info("pid:".$post->id);

@endphp

</div>
