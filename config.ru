require File.expand_path('web', File.dirname(__FILE__))
require 'rubygems'
require 'sinatra'
require './web'

run Sinatra::Application
