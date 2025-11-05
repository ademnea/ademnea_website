@extends('layouts.app')
@section('content')
    <div class="relative p-3 mt-2 overflow-x-auto shadow-md sm:rounded-lg">
        <button type="button" data-modal-target="addFarm" data-modal-show="addFarm"
            class="text-white ml-4 mt-4 bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Add
            New</button>
        <table id="myTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        #
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Farm Name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Address
                    </th>
                    <th scope="col" class="px-6 py-3">
                        District
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @php
                    $count = 1;
                @endphp

                @foreach ($farm as $item)
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $count }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $item->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->address }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->district }}
                        </td>
                        <td class="px-6 py-4 action-links">
                            <!-- Modal toggle -->
                            <div class="flex space-x-2">
                                <a href="#" style="color: white; background-color: #28a745; padding: 8px; border-radius: 5px;" type="button" data-modal-target="large-modal" data-modal-show="large-modal" class="inline-flex items-center" title="View">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path></svg>
                                </a>
                                <a href="#" style="color: white; background-color: #ffc107; padding: 8px; border-radius: 5px;" type="button" data-modal-target="{{ $item->id }}" data-modal-show="{{ $item->id }}" class="inline-flex items-center" title="Edit">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
                                </a>
                                <a href="/admin/hive?farm_id={{ $item->id }}" style="color: white; background-color: #007bff; padding: 8px; border-radius: 5px;" type="button" class="inline-flex items-center" title="Hives">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path></svg>
                                </a>
                                <a href="#" style="color: white; background-color: #dc3545; padding: 8px; border-radius: 5px;" type="button" data-modal-target="popup-modal" data-modal-show="popup-modal" class="inline-flex items-center" title="Delete">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414L7.586 12l-1.293 1.293a1 1 0 101.414 1.414L9 13.414l2.293 2.293a1 1 0 001.414-1.414L11.414 12l1.293-1.293z" clip-rule="evenodd"></path></svg>
                                </a>
                                <a href="{{ url('/farms/map/' . $item->id) }}" style="color: white; background-color: #343a40; padding: 8px; border-radius: 5px;" type="button" class="inline-flex items-center" title="Map">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                                </a>
                            </div>

                        </td>
                    </tr>
                    @php
                        $count = $count + 1;
                    @endphp
                @endforeach
            </tbody>
        </table>

        <!-- Edit  modal -->
        @foreach ($farm as $farm)
            <div id="{{ $farm->id }}" tabindex="-1" aria-hidden="true"
                class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative w-full max-w-2xl max-h-full">
                    <!-- Modal content -->
                    <form action="{{ url('/admin/farm/' . $farm->id) }}" method="POST"
                        class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        {{ method_field('PATCH') }}
                        {{ csrf_field() }}

                        <!-- Modal header -->
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Edit Farm #{{ $farm->id }}
                            </h3>
                            <button type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                data-modal-hide="{{ $farm->id }}">
                                <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Farm Name' }}</label>
                                    <input name="name" type="text" id="name"
                                        value="{{ old('farm_name', $farm->name) }}"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"
                                        required="">
                                </div>
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="last-name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Farm Owner' }}</label>
                                    <input type="text" name="ownerId" id="ownerId"
                                        value="{{ old('farm_owner', $farm->ownerId) }}"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"
                                        required="">
                                </div>
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="address"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Address' }}</label>
                                    <input type="text" name="address" id="address"
                                        value="{{ old('farm_address', $farm->address) }}"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"
                                        placeholder="example@company.com" required="">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="department"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'District' }}</label>
                                    <select type="text" name="district"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"
                                        placeholder="Development" required="">
                                        <option value="Select a district">Select a district</option>
                                        <option value="Abim">Abim</option>
                                        <option value="Adjumani">Adjumani</option>
                                        <option value="Agago">Agago</option>
                                        <option value="Amolatar">Amolatar</option>
                                        <option value="Amudat">Amudat</option>
                                        <option value="Amuru">Amuru</option>
                                        <option value="Apac">Apac</option>
                                        <option value="Arua">Arua</option>
                                        <option value="Budaka">Budaka</option>
                                        <option value="Bududa">Bududa</option>
                                        <option value="Bugiri">Bugiri</option>
                                        <option value="Buhweju">Buhweju</option>
                                        <option value="Buikwe">Buikwe</option>
                                        <option value="Bukedea">Bukedea</option>
                                        <option value="Bukomansimbi">Bukomansimbi</option>
                                        <option value="Bukwo">Bukwo</option>
                                        <option value="Bulambuli">Bulambuli</option>
                                        <option value="Buliisa">Buliisa</option>a</option>
                                        <option value="Bundibugyo">Bundibugyo</option>
                                        <option value="Bunyangabu">Bunyangabu</option>
                                        <option value="Bushenyi">Bushenyi</option>
                                        <option value="Busia">Busia</option>
                                        <option value="Butaleja">Butaleja</option>
                                        <option value="Butambala">Butambala</option>
                                        <option value="Butebo">Butebo</option>
                                        <option value="Buvuma">Buvuma</option>
                                        <option value="Buyende">Buyende</option>
                                        <option value="Dokolo">Dokolo</option>
                                        <option value="Gomba">Gomba</option>
                                        <option value="Gulu">Gulu</option>
                                        <option value="Hoima">Hoima</option>
                                        <option value="Ibanda">Ibanda</option>
                                        <option value="Iganga">Iganga</option>
                                        <option value="Isingiro">Isingiro</option>
                                        <option value="Jinja">Jinja</option>
                                        <option value="Kaabong">Kaabong</option>
                                        <option value="Kabale">Kabale</option>
                                        <option value="Kabarole">Kabarole</option>
                                        <option value="Kaberamaido">Kaberamaido</option>
                                        <option value="Kagadi">Kagadi</option>
                                        <option value="Kalangala">Kalangala</option>
                                        <option value="Kaliro">Kaliro</option>
                                        <option value="Kalungu">Kalungu</option>
                                        <option value="Kampala">Kampala</option>
                                        <option value="Kamuli">Kamuli</option>
                                        <option value="Kamwenge">Kamwenge</option>
                                        <option value="Kanungu">Kanungu</option>
                                        <option value="Kapchorwa">Kapchorwa</option>
                                        <option value="Kasese">Kasese</option>
                                        <option value="Katakwi">Katakwi</option>
                                        <option value="Kayunga">Kayunga</option>
                                        <option value="Kazo">Kazo</option>
                                        <option value="Kibaale">Kibaale</option>
                                        <option value="Kiboga">Kiboga</option>
                                        <option value="Kibuku">Kibuku</option>
                                        <option value="Kikuube">Kikuube</option>
                                        <option value="Kiruhura">Kiruhura</option>
                                        <option value="Kiryandongo">Kiryandongo</option>
                                        <option value="Kisoro">Kisoro</option>
                                        <option value="Kitagwenda">Kitagwenda</option>
                                        <option value="Kitgum">Kitgum</option>
                                        <option value="Koboko">Koboko</option>
                                        <option value="Kole">Kole</option>
                                        <option value="Kotido">Kotido</option>
                                        <option value="Kumi">Kumi</option>
                                        <option value="Kween">Kween</option>
                                        <option value="Kyankwanzi">Kyankwanzi</option>
                                        <option value="Kyegegwa">Kyegegwa</option>
                                        <option value="Kyenjojo">Kyenjojo</option>
                                        <option value="Lamwo">Lamwo</option>
                                        <option value="Lira">Lira</option>
                                        <option value="Luuka">Luuka</option>
                                        <option value="Luuka">Luuka</option>
                                        <option value="Luweero">Luweero</option>
                                        <option value="Lwengo">Lwengo</option>
                                        <option value="Lyantonde">Lyantonde</option>
                                        <option value="Manafwa">Manafwa</option>
                                        <option value="Maracha">Maracha</option>
                                        <option value="Masaka">Masaka</option>
                                        <option value="Masindi">Masindi</option>
                                        <option value="Mayuge">Mayuge</option>
                                        <option value="Mbale">Mbale</option>
                                        <option value="Mbarara">Mbarara</option>
                                        <option value="Mitooma">Mitooma</option>
                                        <option value="Mityana">Mityana</option>
                                        <option value="Moroto">Moroto</option>
                                        <option value="Moyo">Moyo</option>
                                        <option value="Mpigi">Mpigi</option>
                                        <option value="Mubende">Mubende</option>
                                        <option value="Mukono">Mukono</option>
                                        <option value="Nakapiripirit">Nakapiripirit</option>
                                        <option value="Nakaseke">Nakaseke</option>
                                        <option value="Nakasongolo">Nakasongolo</option>
                                        <option value="Namayingo">Namayingo</option>
                                        <option value="Namisindwa">Namisindwa</option>
                                        <option value="Namutumba">Namutumba</option>
                                        <option value="Napak">Napak</option>
                                        <option value="Nebbi">Nebbi</option>
                                        <option value="Ngora">Ngora</option>
                                        <option value="Ntoroko">Ntoroko</option>
                                        <option value="Ntungamo">Ntungamo</option>
                                        <option value="Nwoya">Nwoya</option>
                                        <option value="Omoro">Omoro</option>
                                        <option value="Otuke">Otuke</option>
                                        <option value="Oyam">Oyam</option>
                                        <option value="Ntoroko">Ntoroko</option>
                                        <option value="Ntungamo">Ntungamo</option>
                                        <option value="Nwoya">Nwoya</option>
                                        <option value="Omoro">Omoro</option>
                                        <option value="Otuke">Otuke</option>
                                        <option value="Oyam">Oyam</option>
                                        <option value="Pader">Pader</option>
                                        <option value="Pakwach">Pakwach</option>
                                        <option value="Pallisa">Pallisa</option>
                                        <option value="Rakai">Rakai</option>
                                        <option value="Rubanda">Rubanda</option>
                                        <option value="Rubirizi">Rubirizi</option>
                                        <option value="Rukungiri">Rukungiri</option>
                                        <option value="Sembabule">Sembabule</option>
                                        <option value="Serere">Serere</option>
                                        <option value="Sheema">Sheema</option>
                                        <option value="Sironko">Sironko</option>
                                        <option value="Soroti">Soroti</option>
                                        <option value="Terego">Terego</option>
                                        <option value="Tororo">Tororo</option>
                                        <option value="Wakiso">Wakiso</option>
                                        <option value="Yumbe">Yumbe</option>
                                        <option value="Zombo">Zombo</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button type="submit"
                                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Save
                                all</button>
                        </div>
                    </form>
                </div>
            </div>
    </div>



    <!-- Large Modal -->
    <div id="large-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-4xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                        Farm details
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="large-modal">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6">
                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        Farm name:{{ $farm->name }}
                    </p>
                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        It is found in {{ $farm->address }} which is in {{ $farm->district }} district.
                    </p>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="large-modal" type="button"
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-greeb-700 dark:focus:ring-green-800">Back</button>
                    {{-- <button data-modal-hide="large-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Decline</button> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Delete user -->
    <!-- adding deletig functionality-->
    <!-- Add jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <div id="popup-modal" tabindex="-1"
        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button"
                    class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white"
                    data-modal-hide="popup-modal">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-6 text-center">
                    <svg aria-hidden="true" class="mx-auto mb-4 text-gray-400 w-14 h-14 dark:text-gray-200"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete
                        this Farm member?</h3>
                    <!-- added id to the button Yes, I'm sure-->

                    <form id="delete-user-form-{{ $farm->id }}" method="POST"
                        action="{{ url('admin/farm' . '/' . $farm->id) }}" accept-charset="UTF-8"
                        style="display:inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button id="delete-user-btn" data-modal-hide="popup-modal" type="submit"
                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                            Yes, I'm sure
                        </button>
                    </form>

                    <button data-modal-hide="popup-modal" type="button"
                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No,
                        cancel</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach


    <!-- Add new farm modal -->
    <div id="addFarm" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <form action="{{ url('/admin/farm') }}" method="POST"
                class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                {{ csrf_field() }}

                <!-- Modal header -->
                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Add New Farm
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="addFarm">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-3">
                            <label for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Farm Name' }}</label>
                            <input name="name" type="text" id="name"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"
                                required="">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            {{-- farmer selection dropdown starts here --}}
                            <div class="form-group">
                                <label for="ownerid" class="control-label">{{ 'Farm Owner:' }}</label>
                                <select name="ownerId" id="farmer"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500">
                                    {{-- we will use a loop from the database here --}}
                                    @foreach ($farmers as $farmer)
                                        <option value="{{ $farmer->id }}"> {{ $farmer->fname }} </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- farm owner dropdown stops here. --}}
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="address"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'Address' }}</label>
                            <input type="text" name="address" id="address"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"
                                placeholder="physical address" required="">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="department"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ 'District' }}</label>
                            <select type="text" name="district"
                                class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-600 focus:border-green-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500"
                                placeholder="Development" required="">
                                <option value="Select a district">Select a district</option>
                                <option value="Abim">Abim</option>
                                <option value="Adjumani">Adjumani</option>
                                <option value="Agago">Agago</option>
                                <option value="Amolatar">Amolatar</option>
                                <option value="Amudat">Amudat</option>
                                <option value="Amuru">Amuru</option>
                                <option value="Apac">Apac</option>
                                <option value="Arua">Arua</option>
                                <option value="Budaka">Budaka</option>
                                <option value="Bududa">Bududa</option>
                                <option value="Bugiri">Bugiri</option>
                                <option value="Buhweju">Buhweju</option>
                                <option value="Buikwe">Buikwe</option>
                                <option value="Bukedea">Bukedea</option>
                                <option value="Bukomansimbi">Bukomansimbi</option>
                                <option value="Bukwo">Bukwo</option>
                                <option value="Bulambuli">Bulambuli</option>
                                <option value="Buliisa">Buliisa</option>a</option>
                                <option value="Bundibugyo">Bundibugyo</option>
                                <option value="Bunyangabu">Bunyangabu</option>
                                <option value="Bushenyi">Bushenyi</option>
                                <option value="Busia">Busia</option>
                                <option value="Butaleja">Butaleja</option>
                                <option value="Butambala">Butambala</option>
                                <option value="Butebo">Butebo</option>
                                <option value="Buvuma">Buvuma</option>
                                <option value="Buyende">Buyende</option>
                                <option value="Dokolo">Dokolo</option>
                                <option value="Gomba">Gomba</option>
                                <option value="Gulu">Gulu</option>
                                <option value="Hoima">Hoima</option>
                                <option value="Ibanda">Ibanda</option>
                                <option value="Iganga">Iganga</option>
                                <option value="Isingiro">Isingiro</option>
                                <option value="Jinja">Jinja</option>
                                <option value="Kaabong">Kaabong</option>
                                <option value="Kabale">Kabale</option>
                                <option value="Kabarole">Kabarole</option>
                                <option value="Kaberamaido">Kaberamaido</option>
                                <option value="Kagadi">Kagadi</option>
                                <option value="Kalangala">Kalangala</option>
                                <option value="Kaliro">Kaliro</option>
                                <option value="Kalungu">Kalungu</option>
                                <option value="Kampala">Kampala</option>
                                <option value="Kamuli">Kamuli</option>
                                <option value="Kamwenge">Kamwenge</option>
                                <option value="Kanungu">Kanungu</option>
                                <option value="Kapchorwa">Kapchorwa</option>
                                <option value="Kasese">Kasese</option>
                                <option value="Katakwi">Katakwi</option>
                                <option value="Kayunga">Kayunga</option>
                                <option value="Kazo">Kazo</option>
                                <option value="Kibaale">Kibaale</option>
                                <option value="Kiboga">Kiboga</option>
                                <option value="Kibuku">Kibuku</option>
                                <option value="Kikuube">Kikuube</option>
                                <option value="Kiruhura">Kiruhura</option>
                                <option value="Kiryandongo">Kiryandongo</option>
                                <option value="Kisoro">Kisoro</option>
                                <option value="Kitagwenda">Kitagwenda</option>
                                <option value="Kitgum">Kitgum</option>
                                <option value="Koboko">Koboko</option>
                                <option value="Kole">Kole</option>
                                <option value="Kotido">Kotido</option>
                                <option value="Kumi">Kumi</option>
                                <option value="Kween">Kween</option>
                                <option value="Kyankwanzi">Kyankwanzi</option>
                                <option value="Kyegegwa">Kyegegwa</option>
                                <option value="Kyenjojo">Kyenjojo</option>
                                <option value="Lamwo">Lamwo</option>
                                <option value="Lira">Lira</option>
                                <option value="Luuka">Luuka</option>
                                <option value="Luuka">Luuka</option>
                                <option value="Luweero">Luweero</option>
                                <option value="Lwengo">Lwengo</option>
                                <option value="Lyantonde">Lyantonde</option>
                                <option value="Manafwa">Manafwa</option>
                                <option value="Maracha">Maracha</option>
                                <option value="Masaka">Masaka</option>
                                <option value="Masindi">Masindi</option>
                                <option value="Mayuge">Mayuge</option>
                                <option value="Mbale">Mbale</option>
                                <option value="Mbarara">Mbarara</option>
                                <option value="Mitooma">Mitooma</option>
                                <option value="Mityana">Mityana</option>
                                <option value="Moroto">Moroto</option>
                                <option value="Moyo">Moyo</option>
                                <option value="Mpigi">Mpigi</option>
                                <option value="Mubende">Mubende</option>
                                <option value="Mukono">Mukono</option>
                                <option value="Nakapiripirit">Nakapiripirit</option>
                                <option value="Nakaseke">Nakaseke</option>
                                <option value="Nakasongolo">Nakasongolo</option>
                                <option value="Namayingo">Namayingo</option>
                                <option value="Namisindwa">Namisindwa</option>
                                <option value="Namutumba">Namutumba</option>
                                <option value="Napak">Napak</option>
                                <option value="Nebbi">Nebbi</option>
                                <option value="Ngora">Ngora</option>
                                <option value="Ntoroko">Ntoroko</option>
                                <option value="Ntungamo">Ntungamo</option>
                                <option value="Nwoya">Nwoya</option>
                                <option value="Omoro">Omoro</option>
                                <option value="Otuke">Otuke</option>
                                <option value="Oyam">Oyam</option>
                                <option value="Ntoroko">Ntoroko</option>
                                <option value="Ntungamo">Ntungamo</option>
                                <option value="Nwoya">Nwoya</option>
                                <option value="Omoro">Omoro</option>
                                <option value="Otuke">Otuke</option>
                                <option value="Oyam">Oyam</option>
                                <option value="Pader">Pader</option>
                                <option value="Pakwach">Pakwach</option>
                                <option value="Pallisa">Pallisa</option>
                                <option value="Rakai">Rakai</option>
                                <option value="Rubanda">Rubanda</option>
                                <option value="Rubirizi">Rubirizi</option>
                                <option value="Rukungiri">Rukungiri</option>
                                <option value="Sembabule">Sembabule</option>
                                <option value="Serere">Serere</option>
                                <option value="Sheema">Sheema</option>
                                <option value="Sironko">Sironko</option>
                                <option value="Soroti">Soroti</option>
                                <option value="Terego">Terego</option>
                                <option value="Tororo">Tororo</option>
                                <option value="Wakiso">Wakiso</option>
                                <option value="Yumbe">Yumbe</option>
                                <option value="Zombo">Zombo</option>
                            </select>
                        </div>

                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button type="submit"
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Save
                        all</button>
                </div>
            </form>
        </div>
    </div>
    </div>
    </div>
@endsection

<!-- added pagination and search-->
@section('page_scripts')
    <!-- Include DataTables JS file -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                responsive: true
            });
        });

        // Event listener for delete button
        $(".delete-user-btn").on("click", function() {
            var confirmDelete = confirm("Confirm delete?");
            if (confirmDelete) {
                var form = $(this).closest('form');
                form.submit();
            }
        });

        // Event listener for back button click
        $(".back-button").on("click", function() {
            // Redirect to the team page
            window.location.href = "{{ url('admin/farm') }}";
        });

        // Event listener for back button click
        document.getElementById("back-button").addEventListener("click", function() {
            // Redirect to the team page
            window.location.href = "{{ url('admin/farm') }}"; // Replace with the actual URL of the team page
        });
        // Handle form submission
        $('#addTeamForm').on('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting normally

            // Perform an AJAX request to save the data
            $.ajax({
                url: $(this).attr('action'), // Form action URL
                method: $(this).attr('method'), // Form method (e.g., POST)
                data: new FormData(this), // Form data
                processData: false,
                contentType: false,
                success: function(response) {
                    // Handle the successful response
                    // Update the table with the new row
                    var table = $('#myTable').DataTable(); // Use the updated ID for the table
                    table.row.add([
                        // Add the data to the new row in the table
                        response.name,
                        response.position,
                        response.description,
                        '<a href="#" type="button" data-modal-target="' + response.id +
                        '" data-modal-toggle="' + response.id +
                        '" class="font-medium text-green-600 dark:text-green-500 hover:underline">View</a>' +
                        ' | <a href="#" type="button" data-modal-target="' + response.id +
                        '" data-modal-show="' + response.id +
                        '" class="font-medium text-green-600 dark:text-green-500 hover:underline">Edit</a>' +
                        ' | <a href="#" type="button" data-modal-target="' + response.id +
                        '" data-modal-toggle="' + response.id +
                        '" class="font-medium text-red-600 dark:text-blue-500 hover:underline">Delete</a>'
                    ]).draw(false);

                    // Reset the form
                    $('#addTeamForm')[0].reset();

                    // Close the modal or perform any other necessary actions
                    // ...

                    // Display a success message
                    $('#addTeamForm').append(
                        '<div class="text-green-600">Successfully added a new member.</div>');

                    // Remove the success message after a few seconds
                    setTimeout(function() {
                        $('.text-green-600').remove();
                    }, 3000);

                    // Redirect to the team page
                    window.location.href = "/admin/farm";
                }
            });
        });
    </script>
@endsection
