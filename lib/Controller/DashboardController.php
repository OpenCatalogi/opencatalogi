<?php

namespace OCA\OpenCatalogi\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class DashboardController extends Controller
{
    const TEST_ARRAY = [
        "d021c5ff-a254-4114-a1fb-7a18db152270" => [
            "id" => "d021c5ff-a254-4114-a1fb-7a18db152270",
            "name" => "Dashboard one",
            "summary" => "summary for one"
        ],
        "79c02b33-78ba-4d65-aabd-ff9aae6654f7" => [
            "id" => "79c02b33-78ba-4d65-aabd-ff9aae6654f7",
            "name" => "Dashboard two",
            "summary" => "summary for two"
        ]
    ];

    public function __construct($appName, IRequest $request)
    {
        parent::__construct($appName, $request);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function page(?string $getParameter)
    {
        try {
            return new TemplateResponse(
                $this->appName,
                'index',
                []
            );
        } catch (\Exception $e) {
            return new TemplateResponse(
                $this->appName,
                'error',
                ['error' => $e->getMessage()],
                '500'
            );
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): JSONResponse
    {
        try {
            $results = ["results" => self::TEST_ARRAY];
            return new JSONResponse($results);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
    }
}
