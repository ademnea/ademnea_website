<style>
    .button1{
        background-color: lightseagreen;
        color: white;
        height: 34px;
        width: 75px;
        border-radius: 15px;
        border-color: green;
        shadow: none;
        font-weight: bold;
    }

    .button2{
        background-color: mediumseagreen;
        color: white;
        height: 34px;
        width: 75px;
        border-radius: 15px;
        border-color: green;
        shadow: none;
        font-weight: bold;
    }

    .button3{
        background-color: seagreen;
        color: white;
        height: 34px;
        width: 85px;
        border-radius: 15px;
        border-color: green;
        shadow: none;
        font-weight: bold;
    }

    .button4{
        background-color: lightseagreen;
        color: white;
        height: 40px;
        width: 100px;
        border-radius: 5px;
        border-color: lightseagreen;
        shadow: none;
        font-weight: bold
    }
</style>


<div class="form-group {{ $errors->has('title') ? 'has-error' : ''}}">
    <label for="title" class="control-label">{{ 'Title' }}</label>
    <textarea class="form-control @error('title') is-invalid @enderror" rows="5" name="title" type="textarea" id="title" >{{old('title')}}{{ isset($newsletter->title) ? $newsletter->title : ''}}</textarea>
    @error('title')
        <div class="invalid-feedback mt-2 text-sm">
            {{ $message }}
        </div>
        @enderror
</div>

<div class="form-group {{ $errors->has('description') ? 'has-error' : ''}}">
    <label for="description" class="control-label">{{ 'Article description' }}</label>
    <input class="form-control" name="description" type="text" id="description" value="{{ isset($newsletter->description) ? $newsletter->description : ''}}" required>
    {!! $errors->first('description', '<p class="help-block">:message</p>') !!}
</div>

<div class="form-group">
    <label for="article" class="control-label">{{ 'Article Details' }}</label>
    <textarea class="form-control @error('article') is-invalid @enderror" rows="5" name="article" type="textarea" id="article" >{{old('article')}}{{ isset($newsletter->article) ? $newsletter->article : ''}}</textarea>
    @error('article')
        <div class="invalid-feedback mt-2 text-sm">
            {{ $message }}
        </div>
        @enderror
</div>

<div class="form-group {{ $errors->has('image') ? 'has-error' : ''}}">
    <label for="image" class="control-label">{{ 'Image' }} (jpg, jpeg, png, webp, gif only)</label>
    <input class="form-control @error('image') is-invalid @enderror" name="image" type="file" id="image" accept="image/*">
    @if(isset($newsletter) && !empty($newsletter->image))
        <div class="mt-2">
            <img src="{{ asset($newsletter->image) }}" alt="Newsletter image" style="max-width: 180px; height: auto;">
        </div>
    @endif
    @error('image')
        <div class="invalid-feedback mt-2 text-sm">
            {{ $message }}
        </div>
    @enderror
</div>


<div class="form-group">
    <input class="button4" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    
 CKEDITOR.replace('article', {
        filebrowserUploadUrl: "{{route('upload', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form',
        "extraPlugins" : 'imagebrowser',
		"imageBrowser_listUrl" : "/images_list.json"
    })

    CKEDITOR.replace('title', {
        filebrowserUploadUrl: "{{route('upload', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form',
        "extraPlugins" : 'imagebrowser',
		"imageBrowser_listUrl" : "/images_list.json"
    })
</script>
