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

    sql = "select * from iot_events where event_datetime > CURRENT_DATE - INTERVAL '14 days' order by event_datetime DESC limit 100000;"

    if  verboseLogging:     
        logger.info(f'Exec: {sql}')

    htmlBody = "try exception!"

    try:
        cursor.execute(sql)

        fields = map(lambda x:x[0], cursor.description)
        result = cursor.fetchall()

        records = []
        for row in result:
            row_dict = {0: row[0], 1: row[1], 2: row[2], 3: row[3]}
            records.append(row_dict) 
              
        htmlBody = json.dumps(records, indent=4, sort_keys=True, default=str);
    
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
            'Content-Type': 'application/json'
        },
        'body': htmlBody,
        'isBase64Encoded': False
    }