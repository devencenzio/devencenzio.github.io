require 'sinatra'
require 'rest-client'

NOTIFICATION_PHONE = '2394105303'

# Blowerio - check message count: https://blower.io/dashboard/3b33485a-44dd-42a3-aa2a-302079221b83

logger = Logger.new(STDERR)

helpers do
  # try to limit the message to 160 characters
  def send_sms(message)
    blowerio = RestClient::Resource.new(ENV['BLOWERIO_URL'])
    response = blowerio['/messages'].post :to => '+1' << NOTIFICATION_PHONE,
       :message => message.gsub(/"/, '*').gsub(/[^a-zA-Z0-9~!@#$%^&*()\[\]{};':,.\/<>?| ]/, ' ')
    logger.info "BLOWERIO SMS: #{response.code}"
  end

  def parse_params(params)
    contact_details = Hash.new
    work_details = []
    params.each do |param, value|
      name = param.downcase
      # work types
      work_details.push("Trim") if name == "trim"
      work_details.push("New Construction") if name == "newcon"
      work_details.push("Wood Rot") if name == "woodrot"
      work_details.push("Decks") if name == "decks"
      work_details.push("Drywall") if name == "drywall"
      work_details.push("Renovation") if name == "renovation"
      work_details.push("Stairs") if name == "stairs"

      contact_details[:phone] = value if name == "phone"
      contact_details[:other] = value if name == "other"
      contact_details[:name] = value if name == "name"
      contact_details[:description] = value if name == "message"
      contact_details[:email] = value if name == "email"
    end
    contact_details[:work_details] = work_details
    contact_details
  end

  def report_details(info)
    "From Devencenzio.com: #{info[:name]}, #{info[:phone]}, #{info[:email]}. requesting: #{info[:work_details].inspect}, #{info[:description]} - #{info[:other]}|#{Time.new.strftime('%b %d, %Y')}"
  end
end

get '/' do  #default route
  erb :index
end

get '/contact' do 
  erb :contact
end

get '/gallery' do
  erb :gallery
end

get '/testimonials' do
  erb :testimonials
end

post '/contactform' do
  report = report_details(parse_params(params))
  # TODO - write this record to a database or file
  logger.info "Web Inquiry: \n#{report}\n#{'=' * 32}"
  send_sms(report[0,160]) #must limit the length

  erb :index
end

