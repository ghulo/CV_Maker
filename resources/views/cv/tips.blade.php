{{-- resources/views/cv/tips.blade.php --}}
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-8 mb-8">
                <div class="max-w-3xl mx-auto text-center">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        Këshilla për një CV të Suksesshme
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400">
                        Mësoni si të krijoni një CV që do t'ju ndihmojë të dalloheni nga konkurrentët dhe të siguroni punën tuaj të ëndrrave.
                    </p>
                </div>
            </div>

            <!-- Tips Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($tips as $tip)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-indigo-100 dark:bg-indigo-900 rounded-full p-3">
                                <i class="fas {{ $tip['icon'] }} text-xl text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $tip['title'] }}
                            </h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $tip['description'] }}
                        </p>
                    </div>
                @endforeach
            </div>

            <!-- Additional Resources -->
            <div class="mt-12 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-8">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 text-center">
                    Burime Shtesë
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Video Tutorials -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                            <i class="fas fa-play-circle text-indigo-600 mr-2"></i>
                            Video Udhëzuese
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <i class="fas fa-video text-indigo-600 mt-1 mr-2"></i>
                                <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                                    Si të strukturoni CV-në tuaj
                                </a>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-video text-indigo-600 mt-1 mr-2"></i>
                                <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                                    Gabimet më të shpeshta në CV
                                </a>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-video text-indigo-600 mt-1 mr-2"></i>
                                <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                                    Si të shkruani një përmbledhje efektive
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Articles -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                            <i class="fas fa-book-open text-indigo-600 mr-2"></i>
                            Artikuj të Dobishëm
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <i class="fas fa-file-alt text-indigo-600 mt-1 mr-2"></i>
                                <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                                    10 trendet më të fundit në CV për vitin 2024
                                </a>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-file-alt text-indigo-600 mt-1 mr-2"></i>
                                <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                                    Si të kaloni sistemet ATS të filtrimit të CV-ve
                                </a>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-file-alt text-indigo-600 mt-1 mr-2"></i>
                                <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                                    Aftësitë më të kërkuara nga punëdhënësit
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="mt-12 bg-indigo-600 overflow-hidden shadow-sm sm:rounded-lg p-8 text-center">
                <h3 class="text-2xl font-bold text-white mb-4">
                    Gati për të Krijuar CV-në Tuaj?
                </h3>
                <p class="text-indigo-100 mb-6">
                    Përdorni template-t tona profesionale dhe krijoni CV-në tuaj të përkryer në minuta.
                </p>
                <a href="{{ route('cv.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                    Fillo Tani <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</x-app-layout> 