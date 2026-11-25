<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Response;

class UserCollectionController extends Controller
{
    public function index(User $user): UserResource
    {
        return new UserResource($user->load('media'));
    }

    public function store(User $user, Media $media)
    {
        $attachedIds = $user->media()->syncWithoutDetaching([$media->id])['attached'];
        $status = $attachedIds === []
            ? Response::HTTP_OK
            : Response::HTTP_CREATED;

        return (new UserResource($user->load('media')))
            ->additional([
                'message' => $attachedIds === []
                    ? 'Media sudah ada di koleksi pengguna.'
                    : 'Media berhasil ditambahkan ke koleksi pengguna.',
            ])
            ->response()
            ->setStatusCode($status);
    }

    public function destroy(User $user, Media $media)
    {
        $user->media()->detach($media->id);

        return (new UserResource($user->load('media')))
            ->additional(['message' => 'Media dihapus dari koleksi pengguna.'])
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }
}
