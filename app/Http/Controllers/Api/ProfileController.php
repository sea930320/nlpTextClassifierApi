<?php

namespace App\Http\Controllers\Api;

use App\Services\HelperService;
use App\Services\FacePP\FppClient;

use App\Http\Requests\Profile\ProfileIndex;
use App\Http\Requests\Profile\ProfileStore;
use App\Http\Requests\Profile\ProfileUpdate;

use App\Models\Profile;
use App\Models\Photo;

use Illuminate\Http\JsonResponse;

class ProfileController extends ApiController
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
     * @var HelperService
     */
    private $helperService;

    /**
     * @var FppClient[]
     */
    private $fppClients;
    /**
     * ProfileController constructor.
     *
     * @param Profile $profile
     * @param Photo $photo
     * @param HelperService $helperService
     */
    public function __construct(Profile $profile, Photo $photo, HelperService $helperService)
    {
        $this->profile = $profile;
        $this->photo = $photo;
        $this->helperService = $helperService;

        $apiKeyPairs = config('faceplusplus.apis');
        $this->fppClients = [];
        foreach ($apiKeyPairs as $key => $apiKeyPair) {
            $fppClient = new FppClient($apiKeyPair['api_key'], $apiKeyPair['api_secret'], config('faceplusplus.host'));
            $this->fppClients[] = $fppClient;
        }

        // $this->fppClient = new FppClient(config('faceplusplus.api_key'), config('faceplusplus.api_secret'), config('faceplusplus.host'));
    }

    /**
     * @param ProfileIndex $request
     *
     * @return JsonResponse
     */
    public function index(ProfileIndex $request): JsonResponse
    {
        $profiles = $this->profile
            ->with(['photos'])
            ->get();

        return $this->respond([
            'profiles' => $profiles
        ]);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $profile = $this->profile
            ->with(['photos'])
            ->findOrFail($id);

        return $this->respond($profile);
    }

    /**
     * @param ProfileStore $request
     *
     * @return JsonResponse
     */
    public function store(ProfileStore $request): JsonResponse
    {
        $params = $request->validatedOnly();
        $rootPath = $this->helperService->uniquePath('storage/photos/');
        $params['root_path'] = $rootPath;

        foreach ($this->fppClients as $key => $fppClient) {
            $res = $fppClient->createFaceset([
                'outer_id' => config('faceplusplus.outer_id')
            ]);
            if ($res->status == 200 || ($res->status == 400 && $res->body['error_message'] == 'FACESET_EXIST')) {
                continue;
            } else {
                return $this->respondWithError([
                    'message' => $res->body['error_message']
                ]);
            }
        }
        $profile = $this->profile->create($params);
        return $this->respond(['message' => 'Profile successfully created', 'profile' => $profile]);
    }

    /**
     * @param ProfileUpdate $request
     *
     * @return JsonResponse
     */
    public function update(ProfileUpdate $request): JsonResponse
    {
        $profile = $this->profile
            ->findOrFail($request->input('profile_id'));
        $profile->update($request->validatedOnly());

        return $this->respond(['message' => 'Profile successfully updated', 'profile' => $profile]);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $profile = $this->profile
            ->with(['photos'])
            ->findOrFail($id);
        foreach ($profile->photos as $key => $photo) {
            foreach ($this->fppClients as $key => $fppClient) {
                $resp = $fppClient->removeFaceset([
                    'outer_id' => config('faceplusplus.outer_id'),
                    'face_tokens' => $photo['face_token']
                ]);
                if ($resp->status != 200 && $resp->status != 400) {
                    return $this->respondWithError([
                        'message' => $resp->body['error_message']
                    ]);
                }
            }            
        }
        // if ($profile['outer_id']) {
        //     $res = $this->fppClient->deleteFaceset([
        //         'outer_id' => $profile['outer_id'],
        //         'check_empty' => 0
        //     ]);
        //     if ($res->status != 400 && $res->status != 200) {
        //         return $this->respondWithError([
        //             'message' => 'FacePP Server is not responding, Please try again later'
        //         ]);
        //     }
        // }
        \Storage::deleteDirectory(str_replace("storage", "public", $profile['root_path']));
        $profile->delete();
        return $this->respond(['message' => 'Profile successfully deleted']);
    }
}
