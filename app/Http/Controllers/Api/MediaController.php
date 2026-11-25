<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::query()->with('users')->orderBy('title');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->string('search')->toString().'%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category')->toString());
        }

        return MediaResource::collection($query->get());
    }

    public function store(StoreMediaRequest $request)
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media->load('users')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Media $media)
    {
        return new MediaResource($media->load('users'));
    }

    public function update(UpdateMediaRequest $request, Media $media)
    {
        $media->update($request->validated());

        return new MediaResource($media->load('users'));
    }

    public function destroy(Media $media): Response
    {
        $media->delete();

        return response()->noContent();
    }
}
