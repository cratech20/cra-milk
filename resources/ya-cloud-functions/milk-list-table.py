# coding=utf-8
import os
import logging
import psycopg2
import psycopg2.errors
import datetime as dt
import json
import base64

logger = logging.getLogger()
logger.setLevel(logging.INFO)

verboseLogging = eval(os.environ['VERBOSE_LOG']) ## Convert to bool

if  verboseLogging:
    logger.info('Loading msgHandler function')

def getConnString():
    """
    Extract env variables to connect to DB and return a db string
    Raise an error if the env variables are not set
    :return: string
    """
    db_hostname = os.environ['DB_HOSTNAME']
    db_port =  os.environ['DB_PORT']
    db_name = os.environ['DB_NAME']
    db_user = os.environ['DB_USER']
    db_password = os.environ['DB_PASSWORD']
    db_connection_string = f"host='{db_hostname}' port='{db_port}'  dbname='{db_name}' user='{db_user}' password='{db_password}'  sslmode='require'"
    return db_connection_string

def td(string):
    return f"<td>{str(string)}</td>"

def msgHandler(event = None, context = None):
    statusCode = 500 ## Error response by default

    connection_string = getConnString()
    
    if  verboseLogging:
        logger.info(f'Connecting: {connection_string}')

    conn = psycopg2.connect(connection_string)

    cursor = conn.cursor()

    sql = "select * from iot_events where true order by event_datetime DESC limit 300;"

    if  verboseLogging:     
        logger.info(f'Exec: {sql}')

    try:
        cursor.execute(sql)

        records = cursor.fetchall()

        htmlBody = """
        <!DOCTYPE html>
            <html>
            <head>
            <meta charset="utf-8">
            <title>Список из БД</title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
            </head>
            <body>
        """

        htmlBody += f"Всего строк: {str(cursor.rowcount)}"

        htmlBody += '<table class="table table-striped table-bordered">'

        for row in records:
            tr = ""
            tr += td(row[0])
            tr += td(row[1])
            tr += td(row[2])
            tr += td(row[3])
            htmlBody += f"<tr>{tr}</tr>"

        htmlBody += "</table>"

        statusCode = 200
    except psycopg2.errors.UndefinedTable as error: ## table not exist - create and repeate insert
        conn.rollback()
        logger.error( error)
    except Exception as error:
        logger.error( error)
    conn.commit() # <- We MUST commit to reflect the inserted data
    cursor.close()
    conn.close()

    return {
        'statusCode': statusCode,
        'headers': {
            'Content-Type': 'text/html'
        },
        'body': htmlBody,
        'isBase64Encoded': False
    }