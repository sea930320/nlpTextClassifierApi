<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Photo\PhotoIndex;
use App\Http\Requests\Photo\PhotoStore;
use App\Http\Requests\Photo\PhotoUpdate;

use App\Models\Profile;
use App\Models\Photo;

use Illuminate\Http\JsonResponse;
use App\Services\FacePP\FppClient;

class PhotoController extends ApiController
{
    /**
     * @var Profile
     */
    private $profile;

    /**
     * @var Photo
     */
    private $photo;

    /**
     * @var FppClient[]
     */
    private $fppClients;
    /**
     * PhotoController constructor.
     *
     * @param Profile $profile
     * @param Photo $photo
     */
    public function __construct(Profile $profile, Photo $photo)
    {
        $this->profile = $profile;
        $this->photo = $photo;

        $apiKeyPairs = config('faceplusplus.apis');
        $this->fppClients = [];
        foreach ($apiKeyPairs as $key => $apiKeyPair) {
            $fppClient = new FppClient($apiKeyPair['api_key'], $apiKeyPair['api_secret'], config('faceplusplus.host'));
            $this->fppClients[] = $fppClient;
        }

        // $this->fppClient = new FppClient(config('faceplusplus.api_key'), config('faceplusplus.api_secret'), config('faceplusplus.host'));
    }

    /**
     * @param PhotoIndex $request
     *
     * @return JsonResponse
     */
    public function index(PhotoIndex $request): JsonResponse
    {
        $photos = $this->photo
            ->with(['profile'])
            ->get();

        return $this->respond([
            'photos' => $photos
        ]);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $photo = $this->photo
            ->with(['profile'])
            ->findOrFail($id);

        return $this->respond($photo);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $photo = $this->photo->with('profile')->findOrFail($id);

        foreach ($this->fppClients as $key => $fppClient) {
            $resp = $fppClient->removeFaceset([
                // 'outer_id' => $photo->profile['outer_id'],
                'outer_id' => config('faceplusplus.outer_id'),
                'face_tokens' => $photo['face_token']
            ]);
            if ($resp->status != 200 && $resp->status != 400) {
                return $this->respondWithError([
                    'message' => $resp->body['error_message']
                ]);
            }
        }
        $filePath = str_replace("storage", "public", $photo->profile['root_path']) . '/' . $photo['file_name'];
        \Storage::delete($filePath);
        $photo->delete();

        return $this->respond(['message' => 'Photo successfully deleted']);
    }
}
