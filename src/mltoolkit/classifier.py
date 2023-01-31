import fasttext
import sys
import json
import os
import string
import re

'''
Remove unecessary text from text so we can improve the models parsing accuracy.
'''
def clean_text(sentence):
    cleaned = sentence.lower().strip()
    cleaned = cleaned.translate(str.maketrans('', '', string.punctuation))
    cleaned = re.sub(r'[^\x00-\x7f]',r' ',cleaned)
    cleaned = cleaned.replace("  ", " ")
    return cleaned

'''
Load the config.json at the project root.
'''
def getConfig(model_name):
    config = []
    with open("../config.json", "r") as f:
        config = json.loads(f.read())

    cfg = None
    # Search for the config associated with the relevant model.
    for c in config:
        if c['name'] == model_name:
            cfg = c['classifier']
            break

    if cfg is None:
        raise Exception("Sorry, cannot find model '%s' in config.json" % model_name)

    return cfg

'''
Load and create a fasttext model instance from our pretrained model.
'''
def loadModel(model_name):
    cfg = getConfig(model_name)
    return fasttext.load_model(cfg['model_path'])

'''
Load the previously scraped data generate a training set for our model.
'''
def trainModel(model_name):
    cfg = getConfig(model_name)
    data = None

    with open(cfg['feed_path'], 'r') as f:
        data = f.read()
        data = json.loads(data)

    if data is None:
        raise Exception("Sorry, cannot import feed data for:" % model_name)

    if os.path.isfile(cfg['dataset_path']):
        os.remove(cfg['dataset_path'])

    with open(cfg['dataset_path'], 'w') as f:
        for entity in data:
            f.write(
                "__label__%s %s\n" % (
                     entity[cfg['field']].replace(" ", "_"),
                    clean_text(entity[cfg['text']])
                )
            )


    # You can read more about the various config options here: https://fasttext.cc/docs/en/supervised-tutorial.html
    model = fasttext.train_supervised(
        input=(cfg['dataset_path']),
        epoch=cfg['epochs'],
        wordNgrams=cfg['ngrams'],
        bucket=200000,
        dim=50,
        loss=cfg['loss_function'],
        thread=cfg['threads']
    )

    model.save_model(cfg['model_path'])

'''
This function takes in a model instance and a piece of text and will return
a list of possible predictions and the relevant accuracy score.

Note: if you import this into a Django / Flask project or something similar. You
only need to load your model once on startup and not at every request because depending
on the size of your model that could eat up a lot of memory and slow down performance.

Hence, why the load_model function is seperate from this one.
'''
def predict(model, text):
    result = model.predict(clean_text(text))
    (labels, stats) = result

    results = []
    for i in range(len(labels)):
        accuracy = stats[0]*100
        results.append({"label": labels[i], "accuracy": accuracy})

    return results