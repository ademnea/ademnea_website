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
    <label for="category" class="control-label">{{ 'Category' }}</label>
    <select class="form-select @error('category') is-invalid @enderror" value="{{old('category')}}" name="category" id="category" value="{{ isset($scholarship->category) ? $scholarship->category : ''}}">
  <option value="masters">Masters</option>
  <option value="phd">Phd</option>
</select>
    @error('category')
        <div class="invalid-feedback text-sm">
            {{ $message }}
        </div>
        @enderror
</div>

<div class="form-group">
    <label for="country" class="control-label">{{ 'country' }}</label>
    <select class="form-select @error('category') is-invalid @enderror" value="{{old('country')}}" name="country" id="country" value="{{ isset($scholarship->country) ? $scholarship->country : ''}}">
  <option value="sudan">South sudan</option>
  <option value="uganda">Uganda</option>
  <option value="tanzania">Tanzania</option>
</select>
    @error('country')
        <div class="invalid-feedback text-sm">
            {{ $message }}
        </div>
        @enderror
</div>


<div class="form-group">
    <label for="instructions" class="control-label">{{ 'Instructions' }}</label>
    <textarea class="form-control @error('instructions') is-invalid @enderror" rows="5" name="instructions" type="textarea" id="instructions" >{{old('instructions')}}{{ isset($scholarship->instructions) ? $scholarship->instructions : ''}}</textarea>
    @error('instructions')
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
CKEDITOR.replace( 'instructions' );
</script>