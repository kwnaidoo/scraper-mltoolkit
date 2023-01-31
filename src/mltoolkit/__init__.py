import fasttext
import sys
import json
if len(sys.argv) < 2:
    sys.exit("Please provide a model name you want to train. e.g. : products, spam, adult")

model_name = sys.argv[1].strip()
print(sys.argv)
print("Training: " +model_name+ " \n")

model = fasttext.train_supervised(
    input=(ML_DATA_PATH + "train/" + model_name + ".txt"),
    epoch=200,
    wordNgrams=2,
    bucket=200000,
    dim=50,
    loss='ova',
    thread=30
)

model.save_model(ML_DATA_PATH + "models/" + model_name + ".bin")