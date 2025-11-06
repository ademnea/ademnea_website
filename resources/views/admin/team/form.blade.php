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

<div class="form-group">
    <label for="name" class="control-label">{{ 'Name' }}</label>
    <input class="form-control @error('name') is-invalid @enderror" name="name" type="text" id="name" value="{{ old('name', isset($team->name) ? $team->name : '') }}" >
    @error('name')
        <div class="invalid-feedback text-sm alert">
            {{ $message }}
        </div>
        @enderror
</div>

<div class="form-group">
    <label for="title" class="control-label">{{ 'Title' }}</label>
    <input class="form-control @error('title') is-invalid @enderror" name="title" type="text" id="title" value="{{ old('title', isset($team->title) ? $team->title : '') }}" >
    @error('title')
        <div class="invalid-feedback text-sm">
            {{ $message }}
        </div>
        @enderror
</div>
<div class="form-group">
    <label for="description" class="control-label">{{ 'Research Interests' }}</label>
    <textarea class="form-control @error('description') is-invalid @enderror" rows="5" name="description" id="description">{{ old('description', isset($team->description) ? $team->description : '') }}</textarea>
    @error('description')
        <div class="invalid-feedback mt-2 text-sm">
            {{ $message }}
        </div>
        @enderror
</div>
<div class="form-group">
    <label for="image" class="control-label">{{ 'Image' }}(jpg, peg & png only allowed)</label>
    <input class="form-control @error('image') is-invalid @enderror" name="image" type="file" id="image">
    @error('image')
        <div class="invalid-feedback mt-2 text-sm">
            {{ $message }}
        </div>
        @enderror
</div>
<div class="form-group">
    <input class="button4" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
