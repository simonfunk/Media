<div class="form-group">
    <style>
        .btn-upload {
            margin-bottom: 20px;
        }
        .jsThumbnailImageWrapper figure {
            position: relative;
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 20px
            background-color: #fff;
            border: 1px solid #eee;
            padding: 3px;
            border-radius: 3px;
        }
        .jsThumbnailImageWrapper i {
            position: absolute;
            top:-10px;
            right:-10px;
            color: #f56954;
            font-size: 2em;
            background: white;
            border-radius: 20px;
            height: 25px;
        }
    </style>
    <script>
        if (typeof window.openMediaWindow === 'undefined') {
            window.mediaZone = '';
            window.openMediaWindow = function (event, zone) {
                window.mediaZone = zone;
                window.zoneWrapper = $(event.currentTarget).siblings('.jsThumbnailImageWrapper');
                window.open('{!! route('media.grid.select') !!}', '_blank', 'menubar=no,status=no,toolbar=no,scrollbars=yes,height=500,width=1000');
            };
        }
        if (typeof window.includeMedia === 'undefined') {
            window.includeMedia = function (mediaId) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('api.media.link') }}',
                    data: {
                        'mediaId': mediaId,
                        '_token': '{{ csrf_token() }}',
                        'entityClass': '{{ $entityClass }}',
                        'entityId': '{{ $entityId }}',
                        'zone': window.mediaZone
                    },
                    success: function (data) {
                        var html = '<img src="' + data.result.path + '" alt=""/>' +
                                '<a class="jsRemoveLink" href="#" data-id="' + data.result.imageableId + '">' +
                                '<i class="fa fa-times-circle"></i>' +
                                '</a>';
                        window.zoneWrapper.append(html).fadeIn();
                        if ($fileCount.length > 0) {
                            var count = parseInt($fileCount.text());
                            $fileCount.text(count + 1);
                        }
                    }
                });
            };
        }
    </script>
    {!! Form::label($zone, ucwords(str_replace('_', ' ', $zone)) . ':') !!}
    <div class="clearfix"></div>
    <?php $url = route('media.grid.select') ?>
    <a class="btn btn-primary btn-upload" onclick="openMediaWindow(event, '{{ $zone }}')"><i class="fa fa-upload"></i>
        {{ trans('media::media.Browse') }}
    </a>
    <div class="clearfix"></div>
    <div class="jsThumbnailImageWrapper">
        <?php $zoneVar = "{$zone}Files"  ?>
        <?php if (isset($$zoneVar)): ?>
        <?php foreach ($$zoneVar as $file): ?>
        <figure>
            <img src="{{ Imagy::getThumbnail($file->path, 'mediumThumb') }}" alt=""/>
            <a class="jsRemoveLink" href="#" data-id="{{ $file->pivot->id }}">
                <i class="fa fa-times-circle"></i>
            </a>
        </figure>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<script>
    $( document ).ready(function() {
        $('.jsThumbnailImageWrapper').off('click', '.jsRemoveLink');
        $('.jsThumbnailImageWrapper').on('click', '.jsRemoveLink', function (e) {
            e.preventDefault();
            var imageableId = $(this).data('id'),
                pictureWrapper = $(this).parent(),
                $fileCount = $('.jsFileCount');
            $.ajax({
                type: 'POST',
                url: '{{ route('api.media.unlink') }}',
                data: {
                    'imageableId': imageableId,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.error === false) {
                        pictureWrapper.fadeOut().remove();
                        if ($fileCount.length > 0) {
                            var count = parseInt($fileCount.text());
                            $fileCount.text(count - 1);
                        }
                    } else {
                        pictureWrapper.append(data.message);
                    }
                }
            });
        });
    });
</script>
