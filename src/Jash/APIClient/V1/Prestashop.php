<?php

namespace IDcut\Jash\APIClient\V1;

use IDcut\Jash\APIClient\V1\IDcut as IDCut;
use GuzzleHttp\Exception\RequestException as RequestException;
use GuzzleHttp\Exception\ConnectException as ConnectException;
use GuzzleHttp\Exception\BadResponseException as BadResponseException;
use GuzzleHttp\Exception\ClientException as ClientException;
use GuzzleHttp\Exception\TransferException as TransferException;
use GuzzleHttp\Message\Request as Request;

class Prestashop extends IDCut
{

    public function send($request, $params = array())
    {
        try {
            return $this->httpClient->send($request, $params);
        } catch (RequestException $e) {
            throw new \IDcut\Jash\Exception\Prestashop\Exception($e);
        } catch (ConnectException $e) {
            throw new \IDcut\Jash\Exception\Prestashop\Exception($e);
        } catch (BadResponseException $e) {
            throw new \IDcut\Jash\Exception\Prestashop\Exception($e);
        } catch (ClientException $e) {
            throw new \IDcut\Jash\Exception\Prestashop\Exception($e);
        } catch (TransferException $e) {
            throw new \IDcut\Jash\Exception\Prestashop\Exception($e);
        }
    }
}