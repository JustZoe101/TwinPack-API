<?php

namespace App\Http\Controllers\API;

use App\Http\Data\PackageData;
use App\Http\Controllers\Controller;
use App\Models\Package;
use Spatie\LaravelData\PaginatedDataCollection;

class PackageController extends Controller
{
    /**
     * @return PaginatedDataCollection<int, PackageData>
     */
    public function index(): PaginatedDataCollection
    {
        $packages = Package::with('versions')->paginate();

        return PackageData::collect($packages, PaginatedDataCollection::class);
    }
}
