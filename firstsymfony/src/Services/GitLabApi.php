<?php


namespace App\Services;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GitLabApi
{
    private $urlGitLab;
    private $tokenGitLab;
    private $client;

    public function __construct($urlGitLab, $tokenGitLab, HttpClientInterface $client)
    {
        $this->urlGitLab = $urlGitLab;
        $this->tokenGitLab = $tokenGitLab;
        $this->client = $client;
    }

    public function fetch(): ?array
    {
        try {
            $response = $this->client->request(
                "GET",
                $this->urlGitLab . "projects?owned=true&private_token=" . $this->tokenGitLab
            );
            return $response->toArray();
        }
        catch (TransportExceptionInterface $e) {
            return [];
        } catch (ClientExceptionInterface $e) {
            return [];
        } catch (DecodingExceptionInterface $e) {
            return [];
        } catch (RedirectionExceptionInterface $e) {
            return [];
        } catch (ServerExceptionInterface $e) {
            return [];
        }
    }
    public function fetchMRByProject(int $projectId){
        try {
            $response = $this->client->request(
                "GET",
                $this->urlGitLab . "projects/" . $projectId . "/merge_requests?state=opened&private_token=" .
                $this->tokenGitLab
            );
            return $response->toArray();
        }
        catch (TransportExceptionInterface $e) {
            return [];
        } catch (ClientExceptionInterface $e) {
            return [];
        } catch (DecodingExceptionInterface $e) {
            return [];
        } catch (RedirectionExceptionInterface $e) {
            return [];
        } catch (ServerExceptionInterface $e) {
            return [];
        }
    }
}