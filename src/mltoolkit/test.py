from classifier import trainModel, loadModel, predict

# first train model - you only need to train the model once or if your scraped data changes.
# It will then be saved to a .bin file which you can import and re-use as often as needed.
trainModel('toscraper')

# Now load the .bin file, remember this configuration is set in the config.json in your projects 
# root directory.
model = loadModel('toscraper')

# Now guess the labels that will best describe the sentence or piece of text.
print ("========= PREDICTIONS ===============\n")
print ("<---> Demo: accuracy will be relatively low due to small dataset. <--> \n")
print(predict(model, 'The 10% Entrepreneur: Live'))
